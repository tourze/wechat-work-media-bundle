<?php

namespace WechatWorkMediaBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Repository\TempMediaRepository;
use WechatWorkMediaBundle\Tests\Exception\InvalidFieldException;

/**
 * @internal
 */
#[CoversClass(TempMediaRepository::class)]
#[RunTestsInSeparateProcesses]
final class TempMediaRepositoryTest extends AbstractRepositoryTestCase
{
    private TempMediaRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getService(TempMediaRepository::class);
        self::assertInstanceOf(TempMediaRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testSave(): void
    {
        $entity = $this->createTempMedia();

        $this->repository->save($entity);

        $found = $this->repository->find($entity->getId());
        $this->assertSame($entity, $found);
    }

    public function testSaveWithoutFlush(): void
    {
        $entity = $this->createTempMedia();

        $this->repository->save($entity, false);

        // 即使没有 flush，snowflake ID 也会被分配
        $this->assertNotNull($entity->getId());

        // 但数据库中查不到，因为没有 flush
        $em = self::getService(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $em);
        $em->clear(); // 清除实体管理器缓存

        $found = $this->repository->find($entity->getId());
        $this->assertNull($found);
    }

    public function testRemove(): void
    {
        $entity = $this->createTempMedia();
        $this->repository->save($entity);

        // 确保实体已持久化并有ID
        $this->assertNotNull($entity->getId());
        $entityId = $entity->getId(); // 保存ID以供后续查询

        $this->repository->remove($entity);

        $found = $this->repository->find($entityId);
        $this->assertNull($found);
    }

    public function testQueryWithNullableFields(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setFileKey(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setFileKey('test-key');
        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $nullResults = $this->repository->findBy(['fileKey' => null]);
        $this->assertCount(1, $nullResults);

        $nonNullResults = $this->repository->findBy(['fileKey' => 'test-key']);
        $this->assertCount(1, $nonNullResults);
    }

    public function testCountWithNullableFields(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setFileUrl(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setFileUrl('http://example.com/file.jpg');
        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $nullCount = $this->repository->count(['fileUrl' => null]);
        $this->assertEquals(1, $nullCount);
    }

    public function testQueryWithAssociatedAgent(): void
    {
        $agent = $this->createTestAgent('test-agent-123');

        $entity = $this->createTempMedia();
        $entity->setAgent($agent);
        $this->repository->save($entity);

        $result = $this->repository->findBy(['agent' => $agent]);

        $this->assertCount(1, $result);
        $this->assertSame($agent, $result[0]->getAgent());
    }

    public function testCountWithAssociatedAgent(): void
    {
        $agent = $this->createTestAgent('test-agent-123');

        $entity = $this->createTempMedia();
        $entity->setAgent($agent);
        $this->repository->save($entity);

        $count = $this->repository->count(['agent' => $agent]);

        $this->assertEquals(1, $count);
    }

    public function testFindOneByWithOrderByMultipleFields(): void
    {
        $entity1 = $this->createTempMedia('media1', MediaType::IMAGE);
        $entity2 = $this->createTempMedia('media2', MediaType::IMAGE);
        $entity3 = $this->createTempMedia('media3', MediaType::VIDEO);

        $entity1->setCreateTime(new \DateTimeImmutable('2023-01-01'));
        $entity1->setFileKey('key1');
        $entity2->setCreateTime(new \DateTimeImmutable('2023-01-02'));
        $entity2->setFileKey('key2');
        $entity3->setCreateTime(new \DateTimeImmutable('2023-01-01'));
        $entity3->setFileKey('key3');

        $this->repository->save($entity1);
        $this->repository->save($entity2);
        $this->repository->save($entity3);

        // 测试多字段排序：先按类型，再按创建时间
        $result = $this->repository->findOneBy([], ['type' => 'ASC', 'createTime' => 'ASC']);
        $this->assertNotNull($result);
    }

    public function testQueryWithNullableFieldsExplicitIsNull(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setExpireTime(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setExpireTime(new \DateTimeImmutable('2024-01-01'));

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试显式查询NULL值
        $nullResults = $this->repository->findBy(['expireTime' => null]);
        $this->assertCount(1, $nullResults);
        $this->assertEquals($entity1->getMediaId(), $nullResults[0]->getMediaId());

        // 测试查询非NULL值
        $nonNullResults = $this->repository->findBy(['expireTime' => new \DateTimeImmutable('2024-01-01')]);
        $this->assertCount(1, $nonNullResults);
        $this->assertEquals($entity2->getMediaId(), $nonNullResults[0]->getMediaId());
    }

    public function testCountWithNullableFieldsExplicitIsNull(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setExpireTime(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setExpireTime(new \DateTimeImmutable('2024-01-01'));

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试统计NULL值的记录数
        $nullCount = $this->repository->count(['expireTime' => null]);
        $this->assertEquals(1, $nullCount);

        // 测试统计非NULL值的记录数
        $nonNullCount = $this->repository->count(['expireTime' => new \DateTimeImmutable('2024-01-01')]);
        $this->assertEquals(1, $nonNullCount);
    }

    public function testQueryWithAssociatedAgentNull(): void
    {
        // 清理数据库以确保测试准确性
        self::getEntityManager()->createQuery('DELETE FROM WechatWorkMediaBundle\Entity\TempMedia')->execute();

        $agent = $this->createTestAgent('test-agent-123');

        $entity1 = $this->createTempMedia('media1');
        $entity1->setAgent(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setAgent($agent);

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试查询没有关联代理的记录
        $nullAgentResults = $this->repository->findBy(['agent' => null]);
        $this->assertCount(1, $nullAgentResults);
        $this->assertEquals($entity1->getMediaId(), $nullAgentResults[0]->getMediaId());

        // 测试查询有关联代理的记录
        $withAgentResults = $this->repository->findBy(['agent' => $agent]);
        $this->assertCount(1, $withAgentResults);
        $this->assertEquals($entity2->getMediaId(), $withAgentResults[0]->getMediaId());
    }

    public function testCountWithAssociatedAgentNull(): void
    {
        // 清理数据库以确保测试准确性
        self::getEntityManager()->createQuery('DELETE FROM WechatWorkMediaBundle\Entity\TempMedia')->execute();

        $agent = $this->createTestAgent('test-agent-123');

        $entity1 = $this->createTempMedia('media1');
        $entity1->setAgent(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setAgent($agent);

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试统计没有关联代理的记录数
        $nullAgentCount = $this->repository->count(['agent' => null]);
        $this->assertEquals(1, $nullAgentCount);

        // 测试统计有关联代理的记录数
        $withAgentCount = $this->repository->count(['agent' => $agent]);
        $this->assertEquals(1, $withAgentCount);
    }

    public function testComplexAssociatedAgentQuery(): void
    {
        $agent1 = $this->createTestAgent('test-agent-123');
        $agent2 = $this->createTestAgent('test-agent-456');

        $entity1 = $this->createTempMedia('media1', MediaType::IMAGE);
        $entity1->setAgent($agent1);
        $entity2 = $this->createTempMedia('media2', MediaType::IMAGE);
        $entity2->setAgent($agent2);
        $entity3 = $this->createTempMedia('media3', MediaType::VIDEO);
        $entity3->setAgent($agent1);

        $this->repository->save($entity1);
        $this->repository->save($entity2);
        $this->repository->save($entity3);

        // 测试同时按代理和类型查询
        $results = $this->repository->findBy(['agent' => $agent1, 'type' => MediaType::IMAGE]);
        $this->assertCount(1, $results);
        $this->assertEquals($entity1->getMediaId(), $results[0]->getMediaId());

        // 测试只按代理查询
        $agentResults = $this->repository->findBy(['agent' => $agent1]);
        $this->assertCount(2, $agentResults);
    }

    public function testFindWithNonExistentFieldShouldThrowException(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $managerRegistry->method('getManagerForClass')
            ->willReturn($entityManager)
        ;

        $repository = new class ($managerRegistry) extends TempMediaRepository {
            public function find($id, int|LockMode|null $lockMode = null, ?int $lockVersion = null): ?TempMedia
            {
                if ('nonExistentField' === $id) {
                    throw new InvalidFieldException('Unknown field: nonExistentField');
                }

                return null;
            }
        };

        $this->expectException(InvalidFieldException::class);
        $this->expectExceptionMessage('Unknown field: nonExistentField');

        $repository->find('nonExistentField');
    }

    public function testQueryWithFileUrlNull(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setFileUrl(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setFileUrl('http://example.com/test.jpg');

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试查询文件URL为NULL的记录
        $nullResults = $this->repository->findBy(['fileUrl' => null]);
        $this->assertCount(1, $nullResults);
        $this->assertEquals($entity1->getMediaId(), $nullResults[0]->getMediaId());
    }

    public function testCountWithFileKeyNull(): void
    {
        $entity1 = $this->createTempMedia('media1');
        $entity1->setFileKey(null);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setFileKey('test-key');

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        // 测试统计文件KEY为NULL的记录数
        $nullCount = $this->repository->count(['fileKey' => null]);
        $this->assertEquals(1, $nullCount);
    }

    public function testFindOneByAssociationAgentShouldReturnMatchingEntity(): void
    {
        $agent = $this->createTestAgent('agent-123');

        $entity1 = $this->createTempMedia('media1');
        $entity1->setAgent($agent);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setAgent(null);

        $this->repository->save($entity1);
        $this->repository->save($entity2);

        $result = $this->repository->findOneBy(['agent' => $agent]);
        $this->assertNotNull($result);
        $this->assertEquals($entity1->getMediaId(), $result->getMediaId());
    }

    public function testCountByAssociationAgentShouldReturnCorrectNumber(): void
    {
        $agent1 = $this->createTestAgent('agent-123');
        $agent2 = $this->createTestAgent('agent-456');

        $entity1 = $this->createTempMedia('media1');
        $entity1->setAgent($agent1);
        $entity2 = $this->createTempMedia('media2');
        $entity2->setAgent($agent1);
        $entity3 = $this->createTempMedia('media3');
        $entity3->setAgent($agent2);

        $this->repository->save($entity1);
        $this->repository->save($entity2);
        $this->repository->save($entity3);

        $count = $this->repository->count(['agent' => $agent1]);
        $this->assertEquals(2, $count);
    }

    private function createTempMedia(string $mediaId = 'test-media-id', MediaType $type = MediaType::IMAGE): TempMedia
    {
        $entity = new TempMedia();
        $entity->setMediaId($mediaId);
        $entity->setType($type);
        $entity->setCreateTime(new \DateTimeImmutable());

        return $entity;
    }

    private function createTestAgent(string $agentId): Agent
    {
        $em = self::getService(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $em);

        // 先创建 Corp
        $corp = new Corp();
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpId('test-corp-' . uniqid());
        $corp->setCorpSecret('test-secret');
        $em->persist($corp);

        // 创建 Agent
        $agent = new Agent();
        $agent->setCorp($corp);
        $agent->setAgentId($agentId);
        $agent->setName("Test Agent {$agentId}");
        $agent->setSecret('agent-secret-' . uniqid());
        $em->persist($agent);

        $em->flush();

        return $agent;
    }

    protected function createNewEntity(): object
    {
        $entity = new TempMedia();
        $entity->setMediaId('test-media-' . uniqid());
        $entity->setType(MediaType::IMAGE);
        $entity->setCreateTime(new \DateTimeImmutable());

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<TempMedia>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
