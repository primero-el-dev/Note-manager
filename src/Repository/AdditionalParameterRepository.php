<?php

namespace App\Repository;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use App\Entity\AdditionalParameter;

/**
 * @method AdditionalParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalParameter[]    findAll()
 * @method AdditionalParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalParameterRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalParameter::class);
    }

    public function removeOrphaned()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->remove()
            ->field('note')
            ->equals(null)
            ->getQuery()
            ->execute();
    }
}