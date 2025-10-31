<?php

namespace WechatWorkMediaBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

class TempMediaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tempMedia = new TempMedia();
        $tempMedia->setMediaId('test_media_id_' . uniqid());
        $tempMedia->setType(MediaType::IMAGE);
        $tempMedia->setFileKey('test_file_key');
        $tempMedia->setFileUrl('https://httpbin.org/image/jpeg');
        $tempMedia->setExpireTime(new \DateTimeImmutable('+3 days'));
        $tempMedia->setCreateTime(new \DateTimeImmutable());

        $manager->persist($tempMedia);
        $manager->flush();
    }
}
