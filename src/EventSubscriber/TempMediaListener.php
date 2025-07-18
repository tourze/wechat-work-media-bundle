<?php

namespace WechatWorkMediaBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use League\Flysystem\FilesystemOperator;
use Tourze\TempFileBundle\Service\TemporaryFileService;
use WechatWorkBundle\Service\WorkService;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Request\UploadTempMediaRequest;

/**
 * @see https://www.cnblogs.com/memoyu/p/16267599.html
 */
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: TempMedia::class)]
class TempMediaListener
{
    public function __construct(
        private readonly WorkService $workService,
        private readonly FilesystemOperator $mountManager,
        private readonly TemporaryFileService $temporaryFileService,
    ) {
    }

    /**
     * 保存本地记录前，我们先同步一次到远程
     */
    public function prePersist(TempMedia $media): void
    {
        $request = new UploadTempMediaRequest();
        if ($media->getAgent() instanceof \WechatWorkBundle\Entity\Agent) {
            $request->setAgent($media->getAgent());
        } else {
            $request->setAgent(null);
        }
        $request->setType($media->getType());

        if (!empty($media->getFileKey())) {
            $localFile = $this->temporaryFileService->generateTemporaryFileName('wework_media');
            $content = $this->mountManager->read($media->getFileKey());
            file_put_contents($localFile, $content);
        } else {
            $localFile = $this->temporaryFileService->generateTemporaryFileName('wework_media');
            file_put_contents($localFile, file_get_contents($media->getFileUrl()));
        }

        $request->setMediaFile($localFile);

        $res = $this->workService->request($request);
        $media->setMediaId($res['media_id']);
    }
}
