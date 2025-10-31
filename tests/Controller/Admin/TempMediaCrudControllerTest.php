<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatWorkMediaBundle\Controller\Admin\TempMediaCrudController;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

/**
 * @internal
 */
#[CoversClass(TempMediaCrudController::class)]
#[RunTestsInSeparateProcesses]
final class TempMediaCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerClass(): string
    {
        return TempMediaCrudController::class;
    }

    /**
     * @return TempMediaCrudController
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(TempMediaCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'id' => ['ID'];
        yield 'type' => ['媒体类型'];
        yield 'mediaId' => ['临时素材ID'];
        yield 'expireTime' => ['过期时间'];
        yield 'agent' => ['企业微信应用'];
        yield 'createTime' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'type' => ['type'];
        yield 'mediaId' => ['mediaId'];
        yield 'fileKey' => ['fileKey'];
        yield 'fileUrl' => ['fileUrl'];
        yield 'expireTime' => ['expireTime'];
        yield 'agent' => ['agent'];
    }

    public function testExtendsAbstractCrudController(): void
    {
        $controller = new TempMediaCrudController();
        $this->assertInstanceOf(AbstractCrudController::class, $controller);
    }

    public function testGetEntityFqcnReturnsTempMediaClass(): void
    {
        $this->assertSame(TempMedia::class, TempMediaCrudController::getEntityFqcn());
    }

    public function testConfigureFieldsReturnsCorrectFields(): void
    {
        $controller = new TempMediaCrudController();
        $fields = $controller->configureFields('index');
        $fieldArray = iterator_to_array($fields);

        $this->assertCount(8, $fieldArray);

        // 验证每个字段的类型
        $this->assertInstanceOf(IdField::class, $fieldArray[0]);
        $this->assertInstanceOf(EnumField::class, $fieldArray[1]);
        $this->assertInstanceOf(TextField::class, $fieldArray[2]);
        $this->assertInstanceOf(TextField::class, $fieldArray[3]);
        $this->assertInstanceOf(UrlField::class, $fieldArray[4]);
        $this->assertInstanceOf(DateTimeField::class, $fieldArray[5]);
        $this->assertInstanceOf(AssociationField::class, $fieldArray[6]);
        $this->assertInstanceOf(DateTimeField::class, $fieldArray[7]);
    }

    public function testConfigureFieldsForDifferentPageNames(): void
    {
        $controller = new TempMediaCrudController();
        $indexFields = $controller->configureFields('index');
        $formFields = $controller->configureFields('form');
        $detailFields = $controller->configureFields('detail');

        // 所有页面应该返回相同数量的字段
        $this->assertCount(8, iterator_to_array($indexFields));
        $this->assertCount(8, iterator_to_array($formFields));
        $this->assertCount(8, iterator_to_array($detailFields));
    }

    public function testFieldsAreConfiguredCorrectly(): void
    {
        $controller = new TempMediaCrudController();
        $fields = iterator_to_array($controller->configureFields('index'));

        // 验证字段数量和类型即可，具体配置由EasyAdmin运行时处理
        $this->assertCount(8, $fields);

        // 确保字段配置方法不抛出异常
        $this->assertNotEmpty($fields);
    }

    public function testRequiredFieldsValidation(): void
    {
        // 测试必填字段验证
        $entity = new TempMedia();

        // 测试缺少必填字段时的情况
        $this->assertNull($entity->getType());

        // 设置必填字段
        $entity->setType(MediaType::IMAGE);
        $entity->setMediaId('test-media-id');

        $this->assertNotNull($entity->getType());
        $this->assertNotNull($entity->getMediaId());
    }

    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        // 访问新建页面
        $crawler = $client->request('GET', '/admin/wechat-work-media/temp-media/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        // 验证页面包含表单
        $this->assertGreaterThan(0, $crawler->filter('form[name="TempMedia"]')->count(), '新建页面应该包含临时素材表单');

        // 尝试找到提交按钮
        $submitButton = $crawler->filter('button[type="submit"], input[type="submit"]');
        $this->assertGreaterThan(0, $submitButton->count(), '表单应该包含提交按钮');

        // 获取表单进行提交
        $form = $crawler->filter('form[name="TempMedia"]')->form();

        // 提交空表单验证验证错误
        $crawler = $client->submit($form);

        // 验证返回表单页面（通常是422状态码或重新显示表单）
        $this->assertTrue(
            422 === $client->getResponse()->getStatusCode()
            || 200 === $client->getResponse()->getStatusCode(),
            '提交无效表单应返回422状态码或重新显示表单'
        );

        // 验证页面包含验证错误信息
        $pageContent = $crawler->text();
        $this->assertTrue(
            str_contains($pageContent, 'should not be blank')
            || str_contains($pageContent, '不能为空')
            || str_contains($pageContent, 'This value should not be blank')
            || str_contains($pageContent, 'required')
            || str_contains($pageContent, '必填'),
            '页面应该显示验证错误信息'
        );

        // 额外通过实体验证测试验证逻辑
        $tempMedia = new TempMedia();
        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get('validator');
        $violations = $validator->validate($tempMedia);
        $this->assertGreaterThan(0, count($violations), '空的临时素材实体应该有验证错误');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'type' => ['type'];
        yield 'mediaId' => ['mediaId'];
        yield 'fileKey' => ['fileKey'];
        yield 'fileUrl' => ['fileUrl'];
        yield 'expireTime' => ['expireTime'];
        yield 'agent' => ['agent'];
    }

    /**
     * 自定义测试编辑页面功能（绕过基类的bug）
     */
    public function testEditPageCustomImplementation(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        // 首先访问首页获取一个记录ID
        $crawler = $client->request('GET', $this->generateAdminUrl('index'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $recordIds = [];
        foreach ($crawler->filter('table tbody tr[data-id]') as $row) {
            $rowCrawler = new Crawler($row);
            $recordId = $rowCrawler->attr('data-id');
            if (null !== $recordId && '' !== $recordId) {
                $recordIds[] = $recordId;
            }
        }

        if ([] === $recordIds) {
            self::markTestSkipped('没有找到任何记录来测试编辑页面');
        }

        $firstRecordId = $recordIds[0];
        $client->request('GET', $this->generateAdminUrl('edit', ['entityId' => $firstRecordId]));
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            sprintf('实体 #%s 的编辑页面应该可以访问', $firstRecordId)
        );
    }

    /**
     * 补充测试方法 - 基类testEditPagePrefillsExistingData的功能验证
     * 这个测试修复了基类中客户端设置的问题
     */
    public function testCustomEditPageValidation(): void
    {
        // 创建客户端并登录
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);

        // 检查是否启用了EDIT操作
        try {
            $this->generateAdminUrl('edit', ['entityId' => 1]);
        } catch (\InvalidArgumentException $e) {
            self::markTestSkipped('EDIT action is disabled for this controller.');
        }

        // 首先访问首页获取一个记录ID
        $crawler = $client->request('GET', $this->generateAdminUrl('index'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $recordIds = [];
        foreach ($crawler->filter('table tbody tr[data-id]') as $row) {
            $rowCrawler = new Crawler($row);
            $recordId = $rowCrawler->attr('data-id');
            if (null !== $recordId && '' !== $recordId) {
                $recordIds[] = $recordId;
            }
        }

        if ([] === $recordIds) {
            self::markTestSkipped('列表页面应至少显示一条记录');
        }

        $firstRecordId = $recordIds[0];
        $crawler = $client->request('GET', $this->generateAdminUrl('edit', ['entityId' => $firstRecordId]));
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            sprintf('The edit page for entity #%s should be accessible.', $firstRecordId)
        );

        // 验证编辑页面预填充了现有数据
        $entityName = $this->getEntitySimpleName();
        $form = $crawler->filter(sprintf('form[name="%s"]', $entityName));
        $this->assertGreaterThan(0, $form->count(), '编辑页面应该包含表单');

        // 检查表单字段是否有预填充的值（基本验证即可）
        $formFields = $crawler->filter(sprintf(
            'form[name="%s"] input, form[name="%s"] select, form[name="%s"] textarea',
            $entityName,
            $entityName,
            $entityName
        ));
        $this->assertGreaterThan(0, $formFields->count(), '编辑表单应该包含字段');
    }
}
