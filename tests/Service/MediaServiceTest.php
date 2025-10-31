<?php

namespace WechatWorkMediaBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Exception\FileNotFoundException;
use WechatWorkMediaBundle\Service\MediaService;

/**
 * @internal
 */
#[CoversClass(MediaService::class)]
#[RunTestsInSeparateProcesses]
final class MediaServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for this test
    }

    public function testMediaServiceCanBeInstantiated(): void
    {
        // Service should be available in container and properly configured
        $mediaService = self::getService(MediaService::class);
        $this->assertInstanceOf(MediaService::class, $mediaService);
    }

    public function testMediaServiceHasCorrectConstructorDependencies(): void
    {
        $reflection = new \ReflectionClass(MediaService::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(4, $parameters);

        $expectedTypes = [
            '?Psr\SimpleCache\CacheInterface',
            '?League\Flysystem\FilesystemOperator',
            'Tourze\TempFileBundle\Service\TemporaryFileService',
            'WechatWorkBundle\Service\WorkService',
        ];

        foreach ($parameters as $index => $parameter) {
            $type = $parameter->getType();
            $this->assertNotNull($type);
            $this->assertSame($expectedTypes[$index], (string) $type);
        }
    }

    public function testMediaServiceHasRequiredMethods(): void
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

    public function testMediaServiceMethodDocumentation(): void
    {
        $reflection = new \ReflectionClass(MediaService::class);

        $uploadMethod = $reflection->getMethod('uploadAndGetMediaId');
        $docComment = $uploadMethod->getDocComment();

        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@return string', $docComment);
        $this->assertStringContainsString('@throws', $docComment);

        $downloadMethod = $reflection->getMethod('downloadMedia');
        $this->assertTrue($downloadMethod->hasReturnType());
        $this->assertSame('string', (string) $downloadMethod->getReturnType());
    }

    public function testUploadAndGetMediaIdWithNonExistentFile(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('找不到指定素材文件');

        $mediaService = self::getService(MediaService::class);
        $agent = new Agent();
        $nonExistentPath = '/tmp/non_existent_file.jpg';

        $mediaService->uploadAndGetMediaId($agent, $nonExistentPath, MediaType::IMAGE);
    }

    public function testUploadAndGetMediaIdWithValidFile(): void
    {
        // 涉及 HTTP 请求，需要 Mock WorkService 或者使用单元测试
        self::markTestSkipped('Requires HTTP client mocking for external API calls');
    }

    public function testUploadAndGetMediaIdWithDifferentMediaTypes(): void
    {
        // 涉及 HTTP 请求，需要 Mock WorkService 或者使用单元测试
        self::markTestSkipped('Requires HTTP client mocking for external API calls');
    }

    public function testUploadAndGetMediaIdCaching(): void
    {
        // 涉及 HTTP 请求，需要 Mock WorkService 或者使用单元测试
        self::markTestSkipped('Requires HTTP client mocking for external API calls');
    }

    public function testDownloadMediaSuccessfully(): void
    {
        $mediaService = self::getService(MediaService::class);
        $agent = new Agent();
        $mediaId = 'test_media_id_123';
        $ext = 'jpg';

        $downloadedPath = $mediaService->downloadMedia($agent, $mediaId, $ext);

        $this->assertIsString($downloadedPath);
        $this->assertNotEmpty($downloadedPath);
        $this->assertStringStartsWith('uploads/', $downloadedPath);
        $this->assertStringContainsString('.jpg', $downloadedPath);
    }

    public function testDownloadMediaWithDifferentExtensions(): void
    {
        $mediaService = self::getService(MediaService::class);
        $agent = new Agent();

        $extensions = ['jpg', 'png', 'mp3', 'mp4', 'pdf', 'doc'];

        foreach ($extensions as $ext) {
            $mediaId = "test_media_id_{$ext}";
            $downloadedPath = $mediaService->downloadMedia($agent, $mediaId, $ext);

            $this->assertIsString($downloadedPath, "Downloaded path should be string for extension {$ext}");
            $this->assertStringContainsString(".{$ext}", $downloadedPath, "Downloaded path should contain extension {$ext}");
            $this->assertStringStartsWith('uploads/', $downloadedPath, "Downloaded path should start with uploads/ for extension {$ext}");
        }
    }

    public function testDownloadMediaWithNullExtension(): void
    {
        $mediaService = self::getService(MediaService::class);
        $agent = new Agent();
        $mediaId = 'test_media_id_null_ext';

        $downloadedPath = $mediaService->downloadMedia($agent, $mediaId, null);

        $this->assertIsString($downloadedPath);
        $this->assertStringContainsString('.raw', $downloadedPath, 'Should use .raw extension when none provided');
        $this->assertStringStartsWith('uploads/', $downloadedPath);
    }

    public function testDownloadMediaFileProcessingWorkflow(): void
    {
        // 测试下载媒体文件的完整流程：请求、临时文件创建、内容写入、最终上传
        $mediaService = self::getService(MediaService::class);
        $agent = new Agent();
        $mediaId = 'test_workflow_media_id';
        $ext = 'png';

        $downloadedPath = $mediaService->downloadMedia($agent, $mediaId, $ext);

        // 验证返回的文件路径格式
        $this->assertIsString($downloadedPath);
        $this->assertStringStartsWith('uploads/', $downloadedPath);
        $this->assertStringContainsString('.png', $downloadedPath);
        // 验证文件名包含uniqid生成的随机部分
        $this->assertMatchesRegularExpression('/uploads\/[a-f0-9]+_[a-f0-9]+\.png/', $downloadedPath);
    }

    public function testDownloadMediaWithInvalidResponse(): void
    {
        // 此测试需要Mock WorkService返回非字符串响应
        // 由于我们使用的是集成测试，这里只能测试正常情况
        // 如果需要测试异常情况，应该使用单元测试和Mock
        self::markTestSkipped('This test requires mocking WorkService response, which should be done in unit tests');
    }
}
