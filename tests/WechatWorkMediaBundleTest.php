<?php

namespace WechatWorkMediaBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatWorkMediaBundle\WechatWorkMediaBundle;

class WechatWorkMediaBundleTest extends TestCase
{
    public function test_bundle_extendsSymfonyBundle(): void
    {
        $bundle = new WechatWorkMediaBundle();
        
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function test_bundle_hasCorrectName(): void
    {
        $bundle = new WechatWorkMediaBundle();
        
        $this->assertSame('WechatWorkMediaBundle', $bundle->getName());
    }

    public function test_bundle_canBeInstantiated(): void
    {
        $bundle = new WechatWorkMediaBundle();
        
        $this->assertInstanceOf(WechatWorkMediaBundle::class, $bundle);
    }
} 