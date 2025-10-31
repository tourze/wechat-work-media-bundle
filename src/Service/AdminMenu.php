<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatWorkMediaBundle\Controller\Admin\TempMediaCrudController;

class AdminMenu implements MenuProviderInterface
{
    public function __invoke(ItemInterface $item): void
    {
        $menuItems = self::getMenuItems();

        foreach ($menuItems as $menuItem) {
            // 这里会被EasyAdminMenuBundle处理，添加到菜单中
        }
    }

    /**
     * 获取企业微信媒体管理相关的菜单项
     *
     * @return array<mixed>
     */
    public static function getMenuItems(): array
    {
        return [
            MenuItem::section('企业微信媒体管理', 'fa fa-picture-o'),
            MenuItem::linkToCrud('临时素材', 'fa fa-file-image-o', TempMediaCrudController::getEntityFqcn())
                ->setController(TempMediaCrudController::class),
        ];
    }
}
