<?php

namespace WechatWorkMediaBundle\Service;

use GuzzleHttp\Exception\GuzzleException;
use League\Flysystem\FilesystemOperator;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\TempFileBundle\Service\TemporaryFileService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Exception\FileNotFoundException;
use WechatWorkMediaBundle\Exception\MediaUploadFailedException;
use WechatWorkMediaBundle\Request\MediaGetRequest;
use WechatWorkMediaBundle\Request\UploadRequest;
use Yiisoft\Json\Json;

class MediaService
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly FilesystemOperator $mountManager,
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
    public function uploadAndGetMediaId(Agent $agent, string $path, MediaType $type): string
    {
        // TODO 改造为存数据库，以方便我们排查附件问题
        // 优先查缓存
        $cacheKey = 'WechatWorkBundle_MediaService_uploadAndGetMediaId_' . md5($path) . '_' . $type->value;
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
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
        $request->setAgent($agent);
        $res = $this->workService->request($request);
        if (is_string($res)) {
            $res = Json::decode($res);
        }

        if (!isset($res['media_id'])) {
            throw new MediaUploadFailedException('媒体资源上传失败');
        }

        $this->cache->set($cacheKey, $res['media_id'], 60 * 60 * 24 * 2); // 我们只保留2天，减少一些问题

        return $res['media_id'];
    }

    /**
     * 下载媒体文件，并存储到文件服务器
     */
    public function downloadMedia(Agent $agent, string $mediaId, ?string $ext = null): string
    {
        $request = new MediaGetRequest();
        $request->setAgent($agent);
        $request->setMediaId($mediaId);
        /** @var ResponseInterface $response */
        $response = $this->workService->request($request);
        $tmpPath = $this->temporaryFileService->generateTemporaryFileName('wechat-work-media');
        file_put_contents($tmpPath, $response->getContent());

        // 获取文件名
        $tmpName = uniqid() . '.' . ($ext ?: 'raw');
        $headers = $response->getHeaders();
        $headerValue = $headers['content-disposition'][0];
        if (preg_match('/attachment; filename="(.*?)"/i', $headerValue, $match)) {
            // 优先信任微信返回的文件名
            $tmpName = $match[1];
        }

        // 拼接一个 UploadFile 对象，然后模拟上传一次咯
        $file = $this->mountManager->generateUploadFileFromPath($tmpPath, $tmpName);

        return $this->mountManager->saveUploadFile($file)->getFileKey();
    }
}
