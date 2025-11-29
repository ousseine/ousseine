<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends BaseRepository
{
    private const int LIMIT = 20;

    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllByPublishedAt(int $page): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt <= :now')
            ->andWhere('p.status = :status')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', true)
            ->orderBy('p.publishedAt', 'DESC');

        return $this->pagination(queryBuilder: $queryBuilder, page: $page, fields: ['p.publishedAt']);
    }

    public function findByCategory(Category $category, int $page): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.publishedAt <= :now')
            ->andWhere('p.status = :status')
            ->andWhere('p.category = :category')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', true)
            ->setParameter('category', $category)
            ->orderBy('p.publishedAt', 'DESC');

        return $this->pagination(queryBuilder: $queryBuilder, page: $page, fields: ['p.publishedAt']);
    }

    public function findByAdminPosts(int $page): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');

        return $this->pagination(queryBuilder: $queryBuilder, page: $page, fields: ['p.id', 'p.createdAt']);
    }

    private function pagination(QueryBuilder $queryBuilder, int $page, array $fields): PaginationInterface
    {
        $options = [
            'distinct' => false,
            'sortFieldAllowList' => $fields,
        ];

        return $this->paginator->paginate(target: $queryBuilder, page: $page, limit: self::LIMIT, options: $options);
    }

}
