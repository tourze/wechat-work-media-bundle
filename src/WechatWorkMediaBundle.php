<?php

namespace WechatWorkMediaBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\TempFileBundle\TempFileBundle;
use WechatWorkBundle\WechatWorkBundle;

class WechatWorkMediaBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            TempFileBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
        ];
    }
}
