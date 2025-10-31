<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkMediaBundle\Exception\InvalidFileUrlException;

/**
 * @internal
 */
#[CoversClass(InvalidFileUrlException::class)]
final class InvalidFileUrlExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new InvalidFileUrlException();

        $this->assertInstanceOf(InvalidFileUrlException::class, $exception);
        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Test exception message';
        $exception = new InvalidFileUrlException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Test exception message';
        $code = 42;
        $exception = new InvalidFileUrlException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \RuntimeException('Previous exception');
        $exception = new InvalidFileUrlException('Test message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionIsThrowable(): void
    {
        $this->expectException(InvalidFileUrlException::class);
        $this->expectExceptionMessage('Test throwable exception');

        throw new InvalidFileUrlException('Test throwable exception');
    }
}
