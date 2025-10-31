<?php

namespace WechatWorkMediaBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MediaType: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case IMAGE = 'image';
    case VOICE = 'voice';
    case VIDEO = 'video';
    case FILE = 'file';

    public function getLabel(): string
    {
        return match ($this) {
            self::IMAGE => '图片',
            self::VOICE => '语音',
            self::VIDEO => '视频',
            self::FILE => '普通文件',
        };
    }

    public function getBadgeType(): string
    {
        return match ($this) {
            self::IMAGE => 'success',
            self::VOICE => 'info',
            self::VIDEO => 'warning',
            self::FILE => 'secondary',
        };
    }

    public function getBadge(): string
    {
        return $this->getLabel();
    }

    /**
     * 获取所有枚举的选项数组（用于下拉列表等）
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function toSelectItems(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->getLabel(),
            ];
        }

        return $result;
    }
}
