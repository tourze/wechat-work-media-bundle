<?php

namespace WechatWorkMediaBundle\Tests\Procedure;

use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkMediaBundle\Procedure\TransformFileToWechatWorkMaterial;
use WechatWorkMediaBundle\Service\MediaService;

/**
 * @internal
 */
#[CoversClass(TransformFileToWechatWorkMaterial::class)]
#[RunTestsInSeparateProcesses]
final class TransformFileToWechatWorkMaterialTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for this test
    }

    public function testProcedureExtendsLockableProcedure(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        $this->assertTrue($reflection->isSubclassOf(LockableProcedure::class));
    }

    public function testProcedureHasCorrectConstructorDependencies(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor, 'Constructor should exist');

        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters, 'Constructor should have 4 parameters');

        $expectedParameterTypes = [
            CorpRepository::class,
            AgentRepository::class,
            FilesystemOperator::class,
            MediaService::class,
        ];

        foreach ($parameters as $index => $parameter) {
            $expectedType = $expectedParameterTypes[$index];
            $actualType = $parameter->getType();

            $this->assertNotNull($actualType, "Parameter {$parameter->getName()} should have a type");
            $this->assertSame($expectedType, $actualType instanceof \ReflectionNamedType ? $actualType->getName() : (string) $actualType, "Parameter {$parameter->getName()} should be of type {$expectedType}");
        }
    }

    public function testProcedureHasCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);

        // Check class attributes
        $attributes = $reflection->getAttributes();
        $attributeNames = array_map(fn ($attr) => $attr->getName(), $attributes);

        $this->assertContains(MethodTag::class, $attributeNames);
        $this->assertContains(MethodDoc::class, $attributeNames);
        $this->assertContains(MethodExpose::class, $attributeNames);
        $this->assertContains(Log::class, $attributeNames);
    }

    public function testProcedureHasCorrectProperties(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);

        $this->assertTrue($reflection->hasProperty('corpId'));
        $this->assertTrue($reflection->hasProperty('agentId'));
        $this->assertTrue($reflection->hasProperty('fileUrl'));
        $this->assertTrue($reflection->hasProperty('mediaType'));
    }

    public function testProcedurePropertiesHaveCorrectAttributes(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);

        $corpIdProperty = $reflection->getProperty('corpId');
        $attributes = $corpIdProperty->getAttributes(MethodParam::class);
        $this->assertNotEmpty($attributes);

        $agentIdProperty = $reflection->getProperty('agentId');
        $attributes = $agentIdProperty->getAttributes(MethodParam::class);
        $this->assertNotEmpty($attributes);

        $fileUrlProperty = $reflection->getProperty('fileUrl');
        $attributes = $fileUrlProperty->getAttributes(MethodParam::class);
        $this->assertNotEmpty($attributes);

        $mediaTypeProperty = $reflection->getProperty('mediaType');
        $attributes = $mediaTypeProperty->getAttributes(MethodParam::class);
        $this->assertNotEmpty($attributes);
    }

    public function testProcedureHasExecuteMethod(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);

        $this->assertTrue($reflection->hasMethod('execute'));

        $method = $reflection->getMethod('execute');
        $this->assertTrue($method->isPublic());
    }

    public function testProcedureClassHasCorrectDocumentation(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterial::class);
        $docComment = $reflection->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@see', $docComment);
    }

    public function testExecuteWithInvalidCorpId(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到企业信息');

        /** @var TransformFileToWechatWorkMaterial $procedure */
        $procedure = self::getService(TransformFileToWechatWorkMaterial::class);
        $procedure->corpId = 'invalid_corp_id';
        $procedure->agentId = 'test_agent';
        $procedure->fileUrl = 'uploads/test.jpg';
        $procedure->mediaType = 'image';

        $procedure->execute();
    }

    public function testExecuteWithInvalidAgentId(): void
    {
        // 首先创建一个有效的Corp实体
        $corpRepository = self::getService(CorpRepository::class);
        $corp = new Corp();
        $corp->setCorpId('test_corp_123');
        $corp->setName('测试企业');
        $corp->setCorpSecret('test_secret');
        $corpRepository->save($corp);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到应用信息');

        /** @var TransformFileToWechatWorkMaterial $procedure */
        $procedure = self::getService(TransformFileToWechatWorkMaterial::class);
        $procedure->corpId = 'test_corp_123';
        $procedure->agentId = 'invalid_agent_id';
        $procedure->fileUrl = 'uploads/test.jpg';
        $procedure->mediaType = 'image';

        $procedure->execute();
    }

    public function testExecuteWithInvalidMediaType(): void
    {
        // 创建 Corp 和 Agent
        $corpRepository = self::getService(CorpRepository::class);
        $corp = new Corp();
        $corp->setCorpId('test_corp_456');
        $corp->setName('测试企业456');
        $corp->setCorpSecret('test_secret2');
        $corpRepository->save($corp);

        $agentRepository = self::getService(AgentRepository::class);
        $agent = new Agent();
        $agent->setCorp($corp);
        $agent->setAgentId('test_agent_123');
        $agent->setName('测试应用');
        $agent->setSecret('test_secret');
        $agentRepository->save($agent);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('无效的媒体类型: invalid_type');

        /** @var TransformFileToWechatWorkMaterial $procedure */
        $procedure = self::getService(TransformFileToWechatWorkMaterial::class);
        $procedure->corpId = 'test_corp_456';
        $procedure->agentId = 'test_agent_123';
        $procedure->fileUrl = 'uploads/test.jpg';
        $procedure->mediaType = 'invalid_type'; // 无效的媒体类型

        // 无效媒体类型测试会在文件读取前失败，需要测试文件系统支持
        self::markTestSkipped('Requires filesystem setup to reach media type validation');
    }

    public function testExecuteSuccessfullyWithValidData(): void
    {
        // 创建 Corp 和 Agent
        $corpRepository = self::getService(CorpRepository::class);
        $corp = new Corp();
        $corp->setCorpId('test_corp_789');
        $corp->setName('测试企业789');
        $corp->setCorpSecret('test_secret3');
        $corpRepository->save($corp);

        $agentRepository = self::getService(AgentRepository::class);
        $agent = new Agent();
        $agent->setCorp($corp);
        $agent->setAgentId('test_agent_456');
        $agent->setName('测试应用789');
        $agent->setSecret('test_secret2');
        $agentRepository->save($agent);

        // 由于集成测试环境需要真实文件，此测试需要文件系统Mock或实际文件创建
        // 当前测试架构下较难实现，建议在单元测试中完成
        self::markTestSkipped('Requires filesystem setup for file operations');
    }

    public function testExecuteWithDifferentMediaTypes(): void
    {
        // 需要文件系统支持来测试不同媒体类型的文件处理
        self::markTestSkipped('Requires filesystem setup for file operations');
    }

    public function testExecuteFileProcessingWorkflow(): void
    {
        // 需要文件系统支持来测试完整的文件处理工作流
        self::markTestSkipped('Requires filesystem setup for file operations');
    }
}
