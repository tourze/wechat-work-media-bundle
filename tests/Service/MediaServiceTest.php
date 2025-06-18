<?php

namespace WechatWorkMediaBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Service\MediaService;

class MediaServiceTest extends TestCase
{
    public function test_mediaService_canBeInstantiated(): void
    {
        $cache = $this->createMock(\Psr\SimpleCache\CacheInterface::class);
        $mountManager = $this->createMock(\League\Flysystem\FilesystemOperator::class);
        $temporaryFileService = $this->createMock(\Tourze\TempFileBundle\Service\TemporaryFileService::class);
        $workService = $this->createMock(\WechatWorkBundle\Service\WorkService::class);

        $mediaService = new MediaService(
            $cache,
            $mountManager,
            $temporaryFileService,
            $workService
        );

        $this->assertInstanceOf(MediaService::class, $mediaService);
    }

    public function test_mediaService_hasCorrectConstructorDependencies(): void
    {
        $reflection = new \ReflectionClass(MediaService::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);

        $expectedTypes = [
            'Psr\SimpleCache\CacheInterface',
            'League\Flysystem\FilesystemOperator',
            'Tourze\TempFileBundle\Service\TemporaryFileService',
            'WechatWorkBundle\Service\WorkService'
        ];

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();
            $this->assertNotNull($type);
            $this->assertSame($expectedTypes[$index], $type->getName());
        }
    }

    public function test_mediaService_hasRequiredMethods(): void
    {
        $reflection = new \ReflectionClass(MediaService::class);

        $this->assertTrue($reflection->hasMethod('uploadAndGetMediaId'));
        $this->assertTrue($reflection->hasMethod('downloadMedia'));

        $uploadMethod = $reflection->getMethod('uploadAndGetMediaId');
        $this->assertTrue($uploadMethod->isPublic());
        $this->assertCount(3, $uploadMethod->getParameters());

        $downloadMethod = $reflection->getMethod('downloadMedia');
        $this->assertTrue($downloadMethod->isPublic());
        $this->assertCount(3, $downloadMethod->getParameters());
    }

    public function test_mediaService_methodDocumentation(): void
    {
        $reflection = new \ReflectionClass(MediaService::class);

        $uploadMethod = $reflection->getMethod('uploadAndGetMediaId');
        $docComment = $uploadMethod->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@return string', $docComment);
        $this->assertStringContainsString('@throws', $docComment);

        $downloadMethod = $reflection->getMethod('downloadMedia');
        $this->assertTrue($downloadMethod->hasReturnType());
        $this->assertSame('string', $downloadMethod->getReturnType()->getName());
    }
}
