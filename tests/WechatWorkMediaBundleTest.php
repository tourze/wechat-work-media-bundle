<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\TempFileBundle\TempFileBundle;
use WechatWorkBundle\WechatWorkBundle;
use WechatWorkMediaBundle\WechatWorkMediaBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkMediaBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkMediaBundleTest extends AbstractBundleTestCase
{
    public function testBundleClassExtendsCorrectBaseClass(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaBundle::class);
        $this->assertTrue($reflection->isSubclassOf(Bundle::class));
    }

    public function testBundleClassImplementsBundleDependencyInterface(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaBundle::class);
        $this->assertTrue($reflection->implementsInterface(BundleDependencyInterface::class));
    }

    public function testGetBundleDependenciesReturnsCorrectDependencies(): void
    {
        $dependencies = WechatWorkMediaBundle::getBundleDependencies();

        $expectedDependencies = [
            DoctrineBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            TempFileBundle::class => ['all' => true],
            WechatWorkBundle::class => ['all' => true],
        ];

        $this->assertSame($expectedDependencies, $dependencies);
    }

    public function testBundleDependenciesContainsAllRequiredBundles(): void
    {
        $dependencies = WechatWorkMediaBundle::getBundleDependencies();

        $this->assertArrayHasKey(DoctrineBundle::class, $dependencies);
        $this->assertArrayHasKey(JsonRPCLockBundle::class, $dependencies);
        $this->assertArrayHasKey(TempFileBundle::class, $dependencies);
        $this->assertArrayHasKey(WechatWorkBundle::class, $dependencies);

        foreach ($dependencies as $dependency) {
            $this->assertSame(['all' => true], $dependency);
        }
    }

    public function testBundleClassCanBeInstantiated(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaBundle::class);
        $this->assertTrue($reflection->isInstantiable());
    }

    public function testBundleNameIsCorrect(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaBundle::class);
        $this->assertSame('WechatWorkMediaBundle', $reflection->getShortName());
    }
}
