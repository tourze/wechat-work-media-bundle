<?php

namespace WechatWorkMediaBundle\Tests\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatWorkMediaBundle\Procedure\TransformFileToWechatWorkMaterial;

class TransformFileToWechatWorkMaterialTest extends TestCase
{
    public function test_procedure_canBeInstantiated(): void
    {        $corpRepository = $this->createMock(\WechatWorkBundle\Repository\CorpRepository::class);        $agentRepository = $this->createMock(\WechatWorkBundle\Repository\AgentRepository::class);        $mountManager = $this->createMock(\League\Flysystem\FilesystemOperator::class);        $mediaService = $this->createMock(\WechatWorkMediaBundle\Service\MediaService::class);

        $procedure = new TransformFileToWechatWorkMaterial(
            $corpRepository,
            $agentRepository,
            $mountManager,
            $mediaService
        );

        $this->assertInstanceOf(TransformFileToWechatWorkMaterial::class, $procedure);
    }

    public function test_procedure_extendsLockableProcedure(): void
    {        $corpRepository = $this->createMock(\WechatWorkBundle\Repository\CorpRepository::class);        $agentRepository = $this->createMock(\WechatWorkBundle\Repository\AgentRepository::class);        $mountManager = $this->createMock(\League\Flysystem\FilesystemOperator::class);        $mediaService = $this->createMock(\WechatWorkMediaBundle\Service\MediaService::class);

        $procedure = new TransformFileToWechatWorkMaterial(
            $corpRepository,
            $agentRepository,
            $mountManager,
            $mediaService
        );

        $this->assertInstanceOf(LockableProcedure::class, $procedure);
    }

    public function test_procedure_hasCorrectConstructorDependencies(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);

        $expectedTypes = [
            'WechatWorkBundle\Repository\CorpRepository',
            'WechatWorkBundle\Repository\AgentRepository',
            'League\Flysystem\FilesystemOperator',
            'WechatWorkMediaBundle\Service\MediaService'
        ];

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();
            $this->assertNotNull($type);
            $this->assertSame($expectedTypes[$index], (string) $type);
        }
    }

    public function test_procedure_hasCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        
        // 测试 MethodTag 属性
        $methodTagAttributes = $reflection->getAttributes(MethodTag::class);
        $this->assertCount(1, $methodTagAttributes);
        $this->assertSame('企业微信', $methodTagAttributes[0]->getArguments()['name']);

        // 测试 MethodDoc 属性
        $methodDocAttributes = $reflection->getAttributes(MethodDoc::class);
        $this->assertCount(1, $methodDocAttributes);
        $this->assertSame('转换文件为企微的素材文件', $methodDocAttributes[0]->getArguments()['summary']);

        // 测试 MethodExpose 属性
        $methodExposeAttributes = $reflection->getAttributes(MethodExpose::class);
        $this->assertCount(1, $methodExposeAttributes);
        $this->assertSame('TransformFileToWechatWorkMaterial', $methodExposeAttributes[0]->getArguments()['method']);

        // 测试 Log 属性
        $logAttributes = $reflection->getAttributes(Log::class);
        $this->assertCount(1, $logAttributes);
    }

    public function test_procedure_hasCorrectProperties(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        
        // 测试必需的属性
        $this->assertTrue($reflection->hasProperty('corpId'));
        $this->assertTrue($reflection->hasProperty('agentId'));
        $this->assertTrue($reflection->hasProperty('fileUrl'));
        $this->assertTrue($reflection->hasProperty('mediaType'));

        // 测试属性类型
        $corpIdProperty = $reflection->getProperty('corpId');
        $this->assertTrue($corpIdProperty->isPublic());
        $this->assertSame('string', (string) $corpIdProperty->getType());

        $agentIdProperty = $reflection->getProperty('agentId');
        $this->assertTrue($agentIdProperty->isPublic());
        $this->assertSame('string', (string) $agentIdProperty->getType());

        $fileUrlProperty = $reflection->getProperty('fileUrl');
        $this->assertTrue($fileUrlProperty->isPublic());
        $this->assertSame('string', (string) $fileUrlProperty->getType());

        $mediaTypeProperty = $reflection->getProperty('mediaType');
        $this->assertTrue($mediaTypeProperty->isPublic());
        $this->assertSame('string', (string) $mediaTypeProperty->getType());
    }

    public function test_procedure_propertiesHaveCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        
        // 测试 corpId 属性的 MethodParam 属性
        $corpIdProperty = $reflection->getProperty('corpId');
        $attributes = $corpIdProperty->getAttributes(MethodParam::class);
        $this->assertCount(1, $attributes);
        $this->assertSame('企业ID', $attributes[0]->getArguments()['description']);

        // 测试 agentId 属性的 MethodParam 属性
        $agentIdProperty = $reflection->getProperty('agentId');
        $attributes = $agentIdProperty->getAttributes(MethodParam::class);
        $this->assertCount(1, $attributes);
        $this->assertSame('应用ID', $attributes[0]->getArguments()['description']);

        // 测试 fileUrl 属性的 MethodParam 属性
        $fileUrlProperty = $reflection->getProperty('fileUrl');
        $attributes = $fileUrlProperty->getAttributes(MethodParam::class);
        $this->assertCount(1, $attributes);
        $this->assertSame('文件URL', $attributes[0]->getArguments()['description']);

        // 测试 mediaType 属性的 MethodParam 属性
        $mediaTypeProperty = $reflection->getProperty('mediaType');
        $attributes = $mediaTypeProperty->getAttributes(MethodParam::class);
        $this->assertCount(1, $attributes);
        $this->assertSame('文件类型', $attributes[0]->getArguments()['description']);
    }

    public function test_procedure_hasExecuteMethod(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        
        $this->assertTrue($reflection->hasMethod('execute'));
        
        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(0, $parameters);
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', (string) $returnType);
    }

    public function test_procedure_classHasCorrectDocumentation(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@see', $docComment);
        $this->assertStringContainsString('developer.work.weixin.qq.com', $docComment);
        $this->assertStringContainsString('/document/path/90389', $docComment);
    }
} 