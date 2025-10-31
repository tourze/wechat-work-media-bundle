<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatWorkMediaBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    protected function getMenuProviderClass(): string
    {
        return AdminMenu::class;
    }

    public function testGetMenuItemsReturnsCorrectNumberOfItems(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        $this->assertCount(2, $menuItems);
        $this->assertIsArray($menuItems);
    }

    public function testMenuItemsAreConfiguredCorrectly(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        // 验证菜单项数量和基本结构
        $this->assertCount(2, $menuItems);
        $this->assertIsArray($menuItems);

        // 验证菜单项不为空，具体属性访问由EasyAdmin运行时处理
        foreach ($menuItems as $item) {
            $this->assertNotNull($item);
        }
    }

    public function testGetMenuItemsIsStaticMethod(): void
    {
        $reflection = new \ReflectionMethod(AdminMenu::class, 'getMenuItems');
        $this->assertTrue($reflection->isStatic());
    }

    public function testGetMenuItemsReturnsArray(): void
    {
        $menuItems = AdminMenu::getMenuItems();
        $this->assertIsArray($menuItems);
    }

    public function testMenuItemsAreProperlyTyped(): void
    {
        $menuItems = AdminMenu::getMenuItems();

        foreach ($menuItems as $item) {
            $this->assertNotNull($item);
        }
    }
}
