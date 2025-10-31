<?php

namespace WechatWorkMediaBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkMediaBundle\Enum\MediaType;

/**
 * 上传临时素材
 *
 * @see https://developer.work.weixin.qq.com/document/path/90253
 */
class UploadTempMediaRequest extends ApiRequest
{
    use AgentAware;

    /**
     * @var MediaType 媒体文件类型，分别有图片（image）、语音（voice）、视频（video），普通文件（file）
     */
    private MediaType $type;

    /**
     * @var string 本地文件路径
     */
    private string $mediaFile;

    public function getRequestPath(): string
    {
        return '/cgi-bin/media/upload';
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        $fileHandle = fopen($this->getMediaFile(), 'r');

        return [
            'query' => [
                'type' => $this->getType()->value,
            ],
            'body' => [
                'media' => $fileHandle,
            ],
        ];
    }

    public function getType(): MediaType
    {
        return $this->type;
    }

    public function setType(MediaType $type): void
    {
        $this->type = $type;
    }

    public function getMediaFile(): string
    {
        return $this->mediaFile;
    }

    public function setMediaFile(string $mediaFile): void
    {
        $this->mediaFile = $mediaFile;
    }
}
