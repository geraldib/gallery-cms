<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPostByUserId($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSliderPosts()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    public function getTheLatest()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMostLiked()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.likes', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Post[] Filter User By Name
     */
    public function filterPost($name)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', $name."%")
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

}
