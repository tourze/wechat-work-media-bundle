<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatWorkMediaBundle\WechatWorkMediaBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkMediaBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkMediaBundleTest extends AbstractBundleTestCase
{
}
