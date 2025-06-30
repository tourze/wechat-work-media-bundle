<?php

namespace WechatWorkMediaBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Repository\TempMediaRepository;

#[ORM\Entity(repositoryClass: TempMediaRepository::class)]
#[ORM\Table(name: 'wechat_work_temp_media', options: ['comment' => '临时素材'])]
class TempMedia implements \Stringable
{
    use CreateTimeAware;
    use SnowflakeKeyAware;

    #[ORM\Column(length: 20, enumType: MediaType::class, options: ['comment' => '媒体类型'])]
    private ?MediaType $type = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件KEY'])]
    private ?string $fileKey = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件URL'])]
    private ?string $fileUrl = null;

    #[ORM\Column(length: 120, unique: true, options: ['comment' => '临时素材ID'])]
    private string $mediaId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    private ?\DateTimeImmutable $expireTime = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?AgentInterface $agent = null;

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

    public function getExpireTime(): ?\DateTimeImmutable
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeImmutable $expireTime): static
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    public function getAgent(): ?AgentInterface
    {
        return $this->agent;
    }

    public function setAgent(?AgentInterface $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
