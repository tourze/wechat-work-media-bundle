<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Exception\FileNotFoundException;

class FileNotFoundExceptionTest extends TestCase
{
    public function test_construction_withDefaultValues(): void
    {
        $exception = new FileNotFoundException();
        
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function test_construction_withMessage(): void
    {
        $message = '文件未找到';
        $exception = new FileNotFoundException($message);
        
        $this->assertSame($message, $exception->getMessage());
    }

    public function test_construction_withMessageAndCode(): void
    {
        $message = '文件未找到';
        $code = 404;
        $exception = new FileNotFoundException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function test_construction_withPreviousException(): void
    {
        $previous = new \RuntimeException('原始异常');
        $exception = new FileNotFoundException('文件未找到', 0, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }
} 