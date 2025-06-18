<?php

namespace WechatWorkMediaBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use WechatWorkMediaBundle\Repository\TempMediaRepository;

class TempMediaRepositoryTest extends TestCase
{
    public function test_repository_extendsServiceEntityRepository(): void
    {        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new TempMediaRepository($managerRegistry);
        
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function test_repository_canBeInstantiated(): void
    {        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $repository = new TempMediaRepository($managerRegistry);
        
        $this->assertInstanceOf(TempMediaRepository::class, $repository);
    }

    public function test_repository_hasCorrectPhpDocAnnotations(): void
    {
        $reflection = new \ReflectionClass(TempMediaRepository::class);
        $docComment = $reflection->getDocComment();
        
        $this->assertNotFalse($docComment);
        $this->assertStringContainsString('@method', $docComment);
        $this->assertStringContainsString('TempMedia', $docComment);
    }
} 