<?php

namespace WechatWorkMediaBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use WechatWorkMediaBundle\Entity\TempMedia;

/**
 * @method TempMedia|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempMedia|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempMedia[]    findAll()
 * @method TempMedia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TempMedia::class);
    }
}
