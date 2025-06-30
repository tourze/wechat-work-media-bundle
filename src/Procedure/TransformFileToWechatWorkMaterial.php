<?php

namespace WechatWorkMediaBundle\Procedure;

use League\Flysystem\FilesystemOperator;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Service\MediaService;

/**
 * @see https://developer.work.weixin.qq.com/document/path/90389
 */
#[MethodTag(name: '企业微信')]
#[MethodDoc(summary: '转换文件为企微的素材文件')]
#[MethodExpose(method: 'TransformFileToWechatWorkMaterial')]
#[Log]
class TransformFileToWechatWorkMaterial extends LockableProcedure
{
    #[MethodParam(description: '企业ID')]
    public string $corpId;

    #[MethodParam(description: '应用ID')]
    public string $agentId;

    #[MethodParam(description: '文件URL')]
    public string $fileUrl;

    #[MethodParam(description: '文件类型')]
    public string $mediaType;

    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly FilesystemOperator $mountManager,
        private readonly MediaService $mediaService,
    ) {
    }

    public function execute(): array
    {
        // TODO 这里需要校验文件是否有害喔

        $corp = $this->corpRepository->findOneBy([
            'corpId' => $this->corpId,
        ]);
        if (null === $corp) {
            throw new ApiException('找不到企业信息');
        }

        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $this->agentId,
        ]);
        if (null === $agent) {
            throw new ApiException('找不到应用信息');
        }

        // 先转存文件到本地
        $tmpPath = tempnam(sys_get_temp_dir(), 'wework_material');
        $content = $this->mountManager->read($this->fileUrl);
        file_put_contents($tmpPath, $content);
        $path = $tmpPath;
        // 保存成远程附件
        $mediaId = $this->mediaService->uploadAndGetMediaId($agent, $path, MediaType::tryFrom($this->mediaType));

        return [
            'media_id' => $mediaId,
        ];
    }
}
