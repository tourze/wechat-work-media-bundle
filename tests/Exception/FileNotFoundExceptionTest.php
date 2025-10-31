<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkMediaBundle\Exception\FileNotFoundException;

/**
 * @internal
 */
#[CoversClass(FileNotFoundException::class)]
final class FileNotFoundExceptionTest extends AbstractExceptionTestCase
{
    public function testConstructionWithDefaultValues(): void
    {
        $exception = new FileNotFoundException();

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function testConstructionWithMessage(): void
    {
        $message = '文件未找到';
        $exception = new FileNotFoundException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testConstructionWithMessageAndCode(): void
    {
        $message = '文件未找到';
        $code = 404;
        $exception = new FileNotFoundException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testConstructionWithPreviousException(): void
    {
        $previous = new \RuntimeException('原始异常');
        $exception = new FileNotFoundException('文件未找到', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
