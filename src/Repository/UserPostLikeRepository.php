<?php

namespace App\Repository;

use App\Entity\UserPostLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPostLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPostLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPostLike[]    findAll()
 * @method UserPostLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPostLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPostLike::class);
    }

    public function isLikedPost($user_id, $post_id)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->andWhere('l.post = :post')
            ->setParameter('user', $user_id)
            ->setParameter('post', $post_id)
            ->getQuery()
            ->getResult()
            ;
    }

    public function userLikes($user)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }


    // /**
    //  * @return UserPostLike[] Returns an array of UserPostLike objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPostLike
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
