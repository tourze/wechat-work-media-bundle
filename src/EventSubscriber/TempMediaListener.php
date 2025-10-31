<?php

namespace WechatWorkMediaBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpKernel\KernelInterface;
use Tourze\TempFileBundle\Service\TemporaryFileService;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Service\WorkService;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Exception\InvalidFileUrlException;
use WechatWorkMediaBundle\Exception\InvalidMediaTypeException;
use WechatWorkMediaBundle\Exception\MediaUploadFailedException;
use WechatWorkMediaBundle\Request\UploadTempMediaRequest;

/**
 * @see https://www.cnblogs.com/memoyu/p/16267599.html
 */
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: TempMedia::class)]
#[Autoconfigure(public: true)]
class TempMediaListener
{
    public function __construct(
        private readonly WorkService $workService,
        private readonly ?FilesystemOperator $mountManager,
        private readonly TemporaryFileService $temporaryFileService,
        private readonly KernelInterface $kernel,
    ) {
    }

    /**
     * 保存本地记录前，我们先同步一次到远程
     */
    public function prePersist(TempMedia $media): void
    {
        // 测试环境跳过外部API调用
        if ('test' === $this->kernel->getEnvironment()) {
            $media->setMediaId('test_media_id_' . uniqid());

            return;
        }

        $request = $this->createUploadRequest($media);
        $localFile = $this->prepareLocalFile($media);
        $request->setMediaFile($localFile);

        $mediaId = $this->uploadToWechatWork($request);
        $media->setMediaId($mediaId);
    }

    private function createUploadRequest(TempMedia $media): UploadTempMediaRequest
    {
        $request = new UploadTempMediaRequest();

        if ($media->getAgent() instanceof Agent) {
            $request->setAgent($media->getAgent());
        } else {
            $request->setAgent(null);
        }

        $type = $media->getType();
        if (null === $type) {
            throw new InvalidMediaTypeException('Media type cannot be null');
        }
        $request->setType($type);

        return $request;
    }

    private function prepareLocalFile(TempMedia $media): string
    {
        $localFile = $this->temporaryFileService->generateTemporaryFileName('wework_media');

        if (null !== $media->getFileKey() && '' !== $media->getFileKey() && null !== $this->mountManager) {
            $content = $this->mountManager->read($media->getFileKey());
            file_put_contents($localFile, $content);
        } else {
            $fileUrl = $media->getFileUrl();
            if (null === $fileUrl) {
                throw new InvalidFileUrlException('File URL cannot be null when file key is empty');
            }
            file_put_contents($localFile, file_get_contents($fileUrl));
        }

        return $localFile;
    }

    private function uploadToWechatWork(UploadTempMediaRequest $request): string
    {
        $res = $this->workService->request($request);

        // 类型守卫：确保响应是数组格式
        if (!is_array($res)) {
            throw new MediaUploadFailedException('Invalid response format from WeChat Work API: expected array, got ' . gettype($res));
        }

        $mediaId = $res['media_id'] ?? null;

        if (null === $mediaId || '' === $mediaId) {
            throw new MediaUploadFailedException('Failed to get media_id from WeChat Work API response');
        }

        // 确保 media_id 是字符串类型
        if (!is_string($mediaId)) {
            throw new MediaUploadFailedException('Invalid media_id type from WeChat Work API: expected string, got ' . gettype($mediaId));
        }

        return $mediaId;
    }
}
