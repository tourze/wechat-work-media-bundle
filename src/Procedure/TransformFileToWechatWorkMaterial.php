<?php

namespace WechatWorkMediaBundle\Procedure;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Param\TransformFileToWechatWorkMaterialParam;
use WechatWorkMediaBundle\Service\MediaService;

/**
 * @see https://developer.work.weixin.qq.com/document/path/90389
 */
#[MethodTag(name: '企业微信')]
#[MethodDoc(summary: '转换文件为企微的素材文件')]
#[MethodExpose(method: 'TransformFileToWechatWorkMaterial')]
#[Log]
#[Autoconfigure(public: true)]
class TransformFileToWechatWorkMaterial extends LockableProcedure
{
    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly ?FilesystemOperator $mountManager,
        private readonly MediaService $mediaService,
    ) {
    }

    /**
     * @phpstan-param TransformFileToWechatWorkMaterialParam $param
     */
    public function execute(TransformFileToWechatWorkMaterialParam|RpcParamInterface $param): ArrayResult
    {
        // TODO 这里需要校验文件是否有害喔

        $corp = $this->corpRepository->findOneBy([
            'corpId' => $param->corpId,
        ]);
        if (null === $corp) {
            throw new ApiException('找不到企业信息');
        }

        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $param->agentId,
        ]);
        if (null === $agent) {
            throw new ApiException('找不到应用信息');
        }

        // 先转存文件到本地
        $tmpPath = tempnam(sys_get_temp_dir(), 'wework_material');
        if (null !== $this->mountManager) {
            $content = $this->mountManager->read($param->fileUrl);
            file_put_contents($tmpPath, $content);
        } else {
            file_put_contents($tmpPath, file_get_contents($param->fileUrl));
        }
        $path = $tmpPath;
        // 保存成远程附件
        $mediaType = MediaType::tryFrom($param->mediaType);
        if (null === $mediaType) {
            throw new ApiException('无效的媒体类型: ' . $param->mediaType);
        }
        $mediaId = $this->mediaService->uploadAndGetMediaId($agent, $path, $mediaType);

        return new ArrayResult([
            'media_id' => $mediaId,
        ]);
    }
}
