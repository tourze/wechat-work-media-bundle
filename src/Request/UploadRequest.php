<?php

namespace WechatWorkMediaBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 上传临时素材
 * 素材上传得到media_id，该media_id仅三天内有效,media_id在同一企业内应用之间可以共享
 *
 * @see https://developer.work.weixin.qq.com/document/path/90253
 */
class UploadRequest extends ApiRequest
{
    use AgentAware;

    /**
     * 媒体文件类型，分别有图片（image）、语音（voice）、视频（video），普通文件（file）
     */
    private string $type;

    private string $path;

    public function getRequestPath(): string
    {
        return '/cgi-bin/media/upload';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'multipart' => [
                'name' => 'media',
                'contents' => fopen($this->getPath(), 'r'),
            ],
            'query' => [
                'type' => $this->getType(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }
}
