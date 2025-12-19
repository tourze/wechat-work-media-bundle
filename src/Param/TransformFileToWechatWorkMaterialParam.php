<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * TransformFileToWechatWorkMaterial Procedure 的参数对象
 *
 * 用于转换文件为企微的素材文件
 */
readonly class TransformFileToWechatWorkMaterialParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '企业ID')]
        #[Assert\NotBlank]
        public string $corpId,

        #[MethodParam(description: '应用ID')]
        #[Assert\NotBlank]
        public string $agentId,

        #[MethodParam(description: '文件URL')]
        #[Assert\NotBlank]
        public string $fileUrl,

        #[MethodParam(description: '文件类型')]
        #[Assert\NotBlank]
        public string $mediaType,
    ) {
    }
}
