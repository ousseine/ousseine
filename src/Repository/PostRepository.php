<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllByPublishedAt(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt <= :now')
            ->andWhere('p.status = :status')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', true)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt <= :now')
            ->andWhere('p.status = :status')
            ->andWhere('p.category = :category')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', true)
            ->setParameter('category', $category)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
