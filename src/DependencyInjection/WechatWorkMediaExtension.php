<?php

namespace WechatWorkMediaBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class WechatWorkMediaExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
