<?php

namespace WechatWorkMediaBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatWorkMediaBundle\Exception\InvalidMediaTypeException;

/**
 * @internal
 */
#[CoversClass(InvalidMediaTypeException::class)]
final class InvalidMediaTypeExceptionTest extends AbstractExceptionTestCase
{
    public function testConstructionWithDefaultValues(): void
    {
        $exception = new InvalidMediaTypeException();

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructionWithMessage(): void
    {
        $message = 'Test exception message';
        $exception = new InvalidMediaTypeException($message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructionWithMessageAndCode(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $exception = new InvalidMediaTypeException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testConstructionWithPreviousException(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $previous = new \Exception('Previous exception');
        $exception = new InvalidMediaTypeException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
