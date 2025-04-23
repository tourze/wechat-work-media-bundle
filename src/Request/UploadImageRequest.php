<?php

namespace WechatWorkMediaBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;

/**
 * 上传图片
 * 上传图片得到图片URL，该URL永久有效
 * 返回的图片URL，仅能用于图文消息正文中的图片展示，或者给客户发送欢迎语等；若用于非企业微信环境下的页面，图片将被屏蔽。
 * 每个企业每月最多可上传3000张图片，每天最多可上传1000张图片
 *
 * @see https://developer.work.weixin.qq.com/document/path/90256
 */
class UploadImageRequest extends ApiRequest
{
    use AgentAware;

    private string $path;

    public function getRequestPath(): string
    {
        return '/cgi-bin/media/uploadimg';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'headers' => [
                'Content-Type' => 'image/png',
            ],
            'multipart' => [
                'name' => 'media',
                'contents' => fopen($this->getPath(), 'r'),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
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
