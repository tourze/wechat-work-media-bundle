<?php

declare(strict_types=1);

namespace WechatWorkMediaBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

#[AdminCrud(routePath: '/wechat-work-media/temp-media', routeName: 'wechat_work_media_temp_media')]
final class TempMediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TempMedia::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
        ;

        $typeField = EnumField::new('type', '媒体类型')
            ->setRequired(true)
            ->setHelp('企业微信媒体类型')
        ;
        $typeField->setEnumCases(MediaType::cases());
        yield $typeField;

        yield TextField::new('mediaId', '临时素材ID')
            ->setRequired(true)
            ->setHelp('企业微信返回的临时素材ID')
        ;

        yield TextField::new('fileKey', '文件KEY')
            ->hideOnIndex()
            ->setHelp('存储系统中的文件标识')
        ;

        yield UrlField::new('fileUrl', '文件URL')
            ->hideOnIndex()
            ->setHelp('文件的访问URL')
        ;

        yield DateTimeField::new('expireTime', '过期时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->setHelp('临时素材的过期时间')
        ;

        yield AssociationField::new('agent', '企业微信应用')
            ->autocomplete()
            ->setHelp('关联的企业微信应用')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('临时素材')
            ->setEntityLabelInPlural('临时素材')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('type')
            ->add('mediaId')
            ->add('agent')
            ->add('expireTime')
            ->add('createTime')
        ;
    }
}
