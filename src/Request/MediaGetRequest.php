<?php

namespace WechatWorkMediaBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatWorkBundle\Request\AgentAware;
use WechatWorkBundle\Request\RawResponseInterface;

/**
 * 获取临时素材
 *
 * @see https://developer.work.weixin.qq.com/document/path/90254
 */
class MediaGetRequest extends ApiRequest implements RawResponseInterface
{
    use AgentAware;

    private string $mediaId;

    public function getRequestPath(): string
    {
        return '/cgi-bin/media/get';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'query' => [
                'media_id' => $this->getMediaId(),
            ],
        ];
    }

    public function getRequestMethod(): ?string
    {
        return 'GET';
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }
}
