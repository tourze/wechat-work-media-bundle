<?php

namespace WechatWorkMediaBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MediaType: string implements Labelable, Itemable, Selectable
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
}
