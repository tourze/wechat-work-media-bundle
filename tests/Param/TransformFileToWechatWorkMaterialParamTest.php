<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatWorkMediaBundle\Param\TransformFileToWechatWorkMaterialParam;

/**
 * TransformFileToWechatWorkMaterialParam 单元测试
 *
 * @internal
 */
#[CoversClass(TransformFileToWechatWorkMaterialParam::class)]
final class TransformFileToWechatWorkMaterialParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new TransformFileToWechatWorkMaterialParam(
            corpId: 'corp123',
            agentId: 'agent456',
            fileUrl: 'https://example.com/file.jpg',
            mediaType: 'image',
        );

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new TransformFileToWechatWorkMaterialParam(
            corpId: 'corp123',
            agentId: 'agent456',
            fileUrl: 'https://example.com/file.jpg',
            mediaType: 'image',
        );

        $this->assertSame('corp123', $param->corpId);
        $this->assertSame('agent456', $param->agentId);
        $this->assertSame('https://example.com/file.jpg', $param->fileUrl);
        $this->assertSame('image', $param->mediaType);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterialParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterialParam::class);

        $properties = ['corpId', 'agentId', 'fileUrl', 'mediaType'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationFailsWhenCorpIdIsBlank(): void
    {
        $param = new TransformFileToWechatWorkMaterialParam(
            corpId: '',
            agentId: 'agent456',
            fileUrl: 'https://example.com/file.jpg',
            mediaType: 'image',
        );

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationPassesWithValidParameters(): void
    {
        $param = new TransformFileToWechatWorkMaterialParam(
            corpId: 'corp123',
            agentId: 'agent456',
            fileUrl: 'https://example.com/file.jpg',
            mediaType: 'image',
        );

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(TransformFileToWechatWorkMaterialParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
