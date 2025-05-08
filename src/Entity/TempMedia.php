<?php

namespace WechatWorkMediaBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Repository\TempMediaRepository;

#[AsPermission(title: '临时素材')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: TempMediaRepository::class)]
#[ORM\Table(name: 'wechat_work_temp_media', options: ['comment' => '临时素材'])]
class TempMedia
{
    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[ORM\Column(length: 20, enumType: MediaType::class)]
    private ?MediaType $type = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件KEY'])]
    private ?string $fileKey = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件URL'])]
    private ?string $fileUrl = null;

    #[ORM\Column(length: 120, unique: true, options: ['comment' => '临时素材ID'])]
    private string $mediaId;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expireTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Agent $agent = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function getType(): ?MediaType
    {
        return $this->type;
    }

    public function setType(MediaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFileKey(): ?string
    {
        return $this->fileKey;
    }

    public function setFileKey(?string $fileKey): static
    {
        $this->fileKey = $fileKey;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): static
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeInterface $expireTime): static
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): static
    {
        $this->agent = $agent;

        return $this;
    }
}
