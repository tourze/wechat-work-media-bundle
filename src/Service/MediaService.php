<?php

namespace WechatWorkMediaBundle\Service;

use GuzzleHttp\Exception\GuzzleException;
use League\Flysystem\FilesystemOperator;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\TempFileBundle\Service\TemporaryFileService;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Exception\FileNotFoundException;
use WechatWorkMediaBundle\Exception\MediaUploadFailedException;
use WechatWorkMediaBundle\Request\MediaGetRequest;
use WechatWorkMediaBundle\Request\UploadRequest;

#[Autoconfigure(public: true)]
class MediaService
{
    public function __construct(
        private readonly ?CacheInterface $cache,
        private readonly ?FilesystemOperator $mountManager,
        private readonly TemporaryFileService $temporaryFileService,
        private readonly WorkService $workService,
    ) {
    }

    /**
     * 企业使用自定义的资源时，比如发送本地图片、视频等。为了实现同一资源文件，一次上传可以多次使用，我们提供了素材管理接口：以media_id来标识资源文件，实现文件的上传与下载。
     *
     * @return string 媒体文件上传后获取的唯一标识，3天内有效
     *
     * @throws FileNotFoundException
     * @throws MediaUploadFailedException
     * @throws GuzzleException
     * @throws \JsonException
     * @throws InvalidArgumentException
     */
    public function uploadAndGetMediaId(AgentInterface $agent, string $path, MediaType $type): string
    {
        // TODO 改造为存数据库，以方便我们排查附件问题
        // 优先查缓存
        $cacheKey = 'WechatWorkBundle_MediaService_uploadAndGetMediaId_' . md5($path) . '_' . $type->value;
        if (null !== $this->cache && $this->cache->has($cacheKey)) {
            $cachedMediaId = $this->cache->get($cacheKey);
            if (is_string($cachedMediaId)) {
                return $cachedMediaId;
            }
        }

        if (!file_exists($path)) {
            throw new FileNotFoundException('找不到指定素材文件');
        }

        // {
        //   "errcode": 0,
        //   "errmsg": ""，
        //   "type": "image",
        //   "media_id": "1G6nrLmr5EC3MMb_-zK1dDdzmd0p7cNliYu9V5w7o8K0",
        //   "created_at": "1380000000"
        // }
        $request = new UploadRequest();
        $request->setType($type->value);
        $request->setPath($path);
        if ($agent instanceof Agent) {
            $request->setAgent($agent);
        } else {
            $request->setAgent(null);
        }
        /** @var array<string, mixed> $res */
        $res = $this->workService->request($request);
        // UploadRequest 不实现 RawResponseInterface，WorkService 返回解析后的数组

        if (!isset($res['media_id'])) {
            throw new MediaUploadFailedException('媒体资源上传失败');
        }

        $mediaId = $res['media_id'];
        if (!is_string($mediaId)) {
            throw new MediaUploadFailedException('媒体资源上传失败，返回的 media_id 不是字符串类型');
        }

        $this->cache?->set($cacheKey, $mediaId, 60 * 60 * 24 * 2); // 我们只保留2天，减少一些问题

        return $mediaId;
    }

    /**
     * 下载媒体文件，并存储到文件服务器
     */
    public function downloadMedia(AgentInterface $agent, string $mediaId, ?string $ext = null): string
    {
        $request = new MediaGetRequest();
        if ($agent instanceof Agent) {
            $request->setAgent($agent);
        } else {
            $request->setAgent(null);
        }
        $request->setMediaId($mediaId);
        $response = $this->workService->request($request);
        // MediaGetRequest 实现了 RawResponseInterface，WorkService 应该返回原始字符串内容
        if (!is_string($response)) {
            throw new MediaUploadFailedException('下载媒体文件失败，响应格式不是字符串');
        }
        $responseContent = $response;
        $tmpPath = $this->temporaryFileService->generateTemporaryFileName('wechat-work-media');
        file_put_contents($tmpPath, $responseContent);

        // 获取文件名
        $tmpName = uniqid() . '.' . ($ext ?? 'raw');
        // 注意：由于我们现在直接处理响应内容，无法获取headers信息
        // 如果需要文件名信息，需要通过其他方式获取或者接受ext参数

        // 使用写入替代扩展方法
        $destinationPath = 'uploads/' . uniqid() . '_' . $tmpName;
        if (null !== $this->mountManager) {
            $this->mountManager->writeStream($destinationPath, fopen($tmpPath, 'r'));
        }
        unlink($tmpPath);

        return $destinationPath;
    }
}
