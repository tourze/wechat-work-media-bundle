<?php

namespace WechatWorkMediaBundle\Tests\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\EventSubscriber\TempMediaListener;

class TempMediaListenerTest extends TestCase
{
    public function test_listener_canBeInstantiated(): void
    {        $workService = $this->createMock(\WechatWorkBundle\Service\WorkService::class);        $mountManager = $this->createMock(\League\Flysystem\FilesystemOperator::class);        $temporaryFileService = $this->createMock(\Tourze\TempFileBundle\Service\TemporaryFileService::class);

        $listener = new TempMediaListener(
            $workService,
            $mountManager,
            $temporaryFileService
        );

        $this->assertInstanceOf(TempMediaListener::class, $listener);
    }

    public function test_listener_hasCorrectConstructorDependencies(): void
    {
        $reflection = new \ReflectionClass(TempMediaListener::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(3, $parameters);

        $expectedTypes = [
            'WechatWorkBundle\Service\WorkService',
            'League\Flysystem\FilesystemOperator',
            'Tourze\TempFileBundle\Service\TemporaryFileService'
        ];

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();
            $this->assertNotNull($type);
            $this->assertSame($expectedTypes[$index], (string) $type);
        }
    }

    public function test_listener_hasCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(TempMediaListener::class);
        $attributes = $reflection->getAttributes(AsEntityListener::class);

        $this->assertCount(1, $attributes);
        
        $attribute = $attributes[0];
        $this->assertSame(AsEntityListener::class, $attribute->getName());
        
        $arguments = $attribute->getArguments();
        $this->assertArrayHasKey('event', $arguments);
        $this->assertArrayHasKey('method', $arguments);
        $this->assertArrayHasKey('entity', $arguments);
        
        $this->assertSame(Events::prePersist, $arguments['event']);
        $this->assertSame('prePersist', $arguments['method']);
        $this->assertSame(TempMedia::class, $arguments['entity']);
    }

    public function test_listener_hasPrePersistMethod(): void
    {
        $reflection = new \ReflectionClass(TempMediaListener::class);
        
        $this->assertTrue($reflection->hasMethod('prePersist'));
        
        $method = $reflection->getMethod('prePersist');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);
        
        $parameter = $parameters[0];
        $this->assertSame('media', $parameter->getName());
        
        $type = $parameter->getType();
        $this->assertNotNull($type);
        $this->assertSame(TempMedia::class, (string) $type);
    }

    public function test_listener_methodHasCorrectDocumentation(): void
    {
        $reflection = new \ReflectionClass(TempMediaListener::class);
        $method = $reflection->getMethod('prePersist');
        
        $docComment = $method->getDocComment();
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('保存本地记录前', $docComment);
        $this->assertStringContainsString('同步', $docComment);
        $this->assertStringContainsString('远程', $docComment);
    }

    public function test_listener_classHasCorrectDocumentation(): void
    {
        $reflection = new \ReflectionClass(TempMediaListener::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@see', $docComment);
        $this->assertStringContainsString('cnblogs.com', $docComment);
    }
} 