<?php

namespace WechatWorkMediaBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;
use WechatWorkMediaBundle\Enum\MediaType;

/**
 * @internal
 */
#[CoversClass(MediaType::class)]
final class MediaTypeTest extends AbstractEnumTestCase
{
    #[TestWith([MediaType::IMAGE, 'image', '图片'])]
    #[TestWith([MediaType::VOICE, 'voice', '语音'])]
    #[TestWith([MediaType::VIDEO, 'video', '视频'])]
    #[TestWith([MediaType::FILE, 'file', '普通文件'])]
    public function testEnumValueAndLabel(MediaType $case, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $case->value);
        $this->assertSame($expectedLabel, $case->getLabel());
    }

    #[TestWith(['image', MediaType::IMAGE])]
    #[TestWith(['voice', MediaType::VOICE])]
    #[TestWith(['video', MediaType::VIDEO])]
    #[TestWith(['file', MediaType::FILE])]
    public function testTryFromWithValidValues(string $value, MediaType $expected): void
    {
        $this->assertSame($expected, MediaType::tryFrom($value));
    }

    #[TestWith(['image', MediaType::IMAGE])]
    #[TestWith(['voice', MediaType::VOICE])]
    #[TestWith(['video', MediaType::VIDEO])]
    #[TestWith(['file', MediaType::FILE])]
    public function testFromWithValidValues(string $value, MediaType $expected): void
    {
        $this->assertSame($expected, MediaType::from($value));
    }

    public function testCasesReturnsAllCases(): void
    {
        $cases = MediaType::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(MediaType::IMAGE, $cases);
        $this->assertContains(MediaType::VOICE, $cases);
        $this->assertContains(MediaType::VIDEO, $cases);
        $this->assertContains(MediaType::FILE, $cases);
    }

    public function testImplementsRequiredInterfaces(): void
    {
        $reflection = new \ReflectionEnum(MediaType::class);

        $this->assertTrue($reflection->implementsInterface(Labelable::class));
        $this->assertTrue($reflection->implementsInterface(Itemable::class));
        $this->assertTrue($reflection->implementsInterface(Selectable::class));
    }

    public function testHasRequiredTraits(): void
    {
        $reflection = new \ReflectionEnum(MediaType::class);
        $traits = $reflection->getTraitNames();

        $this->assertContains(ItemTrait::class, $traits);
        $this->assertContains(SelectTrait::class, $traits);
    }

    #[TestWith([MediaType::IMAGE, 'image', '图片'])]
    #[TestWith([MediaType::VOICE, 'voice', '语音'])]
    #[TestWith([MediaType::VIDEO, 'video', '视频'])]
    #[TestWith([MediaType::FILE, 'file', '普通文件'])]
    public function testToArray(MediaType $case, string $expectedValue, string $expectedLabel): void
    {
        $array = $case->toSelectItem();

        $this->assertIsArray($array);
        $this->assertCount(4, $array);

        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('name', $array);

        $this->assertSame($expectedValue, $array['value']);
        $this->assertSame($expectedLabel, $array['label']);
        $this->assertSame($expectedLabel, $array['text']);
        $this->assertSame($expectedLabel, $array['name']);
    }

    public function testValueUniqueness(): void
    {
        $values = [];
        foreach (MediaType::cases() as $case) {
            $values[] = $case->value;
        }

        $uniqueValues = array_unique($values);
        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelUniqueness(): void
    {
        $labels = [];
        foreach (MediaType::cases() as $case) {
            $labels[] = $case->getLabel();
        }

        $uniqueLabels = array_unique($labels);
        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testGenOptions(): void
    {
        $options = MediaType::genOptions();

        $this->assertIsArray($options);
        $this->assertCount(4, $options);

        foreach ($options as $option) {
            $this->assertIsArray($option);
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }

        // Verify specific options are present
        $values = array_column($options, 'value');
        $this->assertContains('image', $values);
        $this->assertContains('voice', $values);
        $this->assertContains('video', $values);
        $this->assertContains('file', $values);
    }
}
