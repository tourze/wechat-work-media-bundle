<?php

namespace WechatWorkMediaBundle\Tests\Enum;

use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Enum\MediaType;

class MediaTypeTest extends TestCase
{
    public function test_enumValues_containsAllExpectedCases(): void
    {
        $this->assertSame('image', MediaType::IMAGE->value);
        $this->assertSame('voice', MediaType::VOICE->value);
        $this->assertSame('video', MediaType::VIDEO->value);
        $this->assertSame('file', MediaType::FILE->value);
    }

    public function test_getLabel_returnsCorrectLabels(): void
    {
        $this->assertSame('图片', MediaType::IMAGE->getLabel());
        $this->assertSame('语音', MediaType::VOICE->getLabel());
        $this->assertSame('视频', MediaType::VIDEO->getLabel());
        $this->assertSame('普通文件', MediaType::FILE->getLabel());
    }

    public function test_tryFrom_withValidValues(): void
    {
        $this->assertSame(MediaType::IMAGE, MediaType::tryFrom('image'));
        $this->assertSame(MediaType::VOICE, MediaType::tryFrom('voice'));
        $this->assertSame(MediaType::VIDEO, MediaType::tryFrom('video'));
        $this->assertSame(MediaType::FILE, MediaType::tryFrom('file'));
    }

    public function test_tryFrom_withInvalidValue(): void
    {
        $this->assertNull(MediaType::tryFrom('invalid'));
        $this->assertNull(MediaType::tryFrom(''));
        $this->assertNull(MediaType::tryFrom('Image'));
    }

    public function test_from_withValidValues(): void
    {
        $this->assertSame(MediaType::IMAGE, MediaType::from('image'));
        $this->assertSame(MediaType::VOICE, MediaType::from('voice'));
        $this->assertSame(MediaType::VIDEO, MediaType::from('video'));
        $this->assertSame(MediaType::FILE, MediaType::from('file'));
    }

    public function test_from_withInvalidValue(): void
    {
        $this->expectException(\ValueError::class);
        MediaType::from('invalid');
    }

    public function test_cases_returnsAllCases(): void
    {
        $cases = MediaType::cases();
        
        $this->assertCount(4, $cases);
        $this->assertContains(MediaType::IMAGE, $cases);
        $this->assertContains(MediaType::VOICE, $cases);
        $this->assertContains(MediaType::VIDEO, $cases);
        $this->assertContains(MediaType::FILE, $cases);
    }

    public function test_implementsRequiredInterfaces(): void
    {
        $reflection = new \ReflectionEnum(MediaType::class);
        
        $this->assertTrue($reflection->implementsInterface(\Tourze\EnumExtra\Labelable::class));
        $this->assertTrue($reflection->implementsInterface(\Tourze\EnumExtra\Itemable::class));
        $this->assertTrue($reflection->implementsInterface(\Tourze\EnumExtra\Selectable::class));
    }

    public function test_hasRequiredTraits(): void
    {
        $reflection = new \ReflectionEnum(MediaType::class);
        $traits = $reflection->getTraitNames();
        
        $this->assertContains(\Tourze\EnumExtra\ItemTrait::class, $traits);
        $this->assertContains(\Tourze\EnumExtra\SelectTrait::class, $traits);
    }
} 