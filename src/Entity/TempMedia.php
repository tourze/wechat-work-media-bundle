<?php

namespace WechatWorkMediaBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Repository\TempMediaRepository;

#[ORM\Entity(repositoryClass: TempMediaRepository::class)]
#[ORM\Table(name: 'wechat_work_temp_media', options: ['comment' => '临时素材'])]
class TempMedia implements \Stringable
{
    use CreateTimeAware;
    use SnowflakeKeyAware;

    #[ORM\Column(length: 20, enumType: MediaType::class, options: ['comment' => '媒体类型'])]
    #[Assert\Choice(callback: [MediaType::class, 'cases'])]
    private ?MediaType $type = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件KEY'])]
    #[Assert\Length(max: 255)]
    private ?string $fileKey = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '文件URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $fileUrl = null;

    #[ORM\Column(length: 120, unique: true, options: ['comment' => '临时素材ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private string $mediaId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '过期时间'])]
    #[Assert\Type(type: \DateTimeImmutable::class)]
    private ?\DateTimeImmutable $expireTime = null;

    #[ORM\ManyToOne(targetEntity: Agent::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Agent $agent = null;

    public function getType(): ?MediaType
    {
        return $this->type;
    }

    public function setType(MediaType $type): void
    {
        $this->type = $type;
    }

    public function getFileKey(): ?string
    {
        return $this->fileKey;
    }

    public function setFileKey(?string $fileKey): void
    {
        $this->fileKey = $fileKey;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): void
    {
        $this->fileUrl = $fileUrl;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getExpireTime(): ?\DateTimeImmutable
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeImmutable $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): void
    {
        $this->agent = $agent;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
