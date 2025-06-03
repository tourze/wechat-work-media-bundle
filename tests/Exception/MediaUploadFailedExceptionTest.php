<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Exception\MediaUploadFailedException;

class MediaUploadFailedExceptionTest extends TestCase
{
    public function test_construction_withDefaultValues(): void
    {
        $exception = new MediaUploadFailedException();
        
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function test_construction_withMessage(): void
    {
        $message = '媒体上传失败';
        $exception = new MediaUploadFailedException($message);
        
        $this->assertSame($message, $exception->getMessage());
    }

    public function test_construction_withMessageAndCode(): void
    {
        $message = '媒体上传失败';
        $code = 500;
        $exception = new MediaUploadFailedException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function test_construction_withPreviousException(): void
    {
        $previous = new \RuntimeException('网络错误');
        $exception = new MediaUploadFailedException('媒体上传失败', 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
} 