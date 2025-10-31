<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkMediaBundle\Exception\MediaUploadFailedException;

/**
 * @internal
 */
#[CoversClass(MediaUploadFailedException::class)]
final class MediaUploadFailedExceptionTest extends AbstractExceptionTestCase
{
    public function testConstructionWithDefaultValues(): void
    {
        $exception = new MediaUploadFailedException();

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function testConstructionWithMessage(): void
    {
        $message = '媒体上传失败';
        $exception = new MediaUploadFailedException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testConstructionWithMessageAndCode(): void
    {
        $message = '媒体上传失败';
        $code = 500;
        $exception = new MediaUploadFailedException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testConstructionWithPreviousException(): void
    {
        $previous = new \RuntimeException('网络错误');
        $exception = new MediaUploadFailedException('媒体上传失败', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
