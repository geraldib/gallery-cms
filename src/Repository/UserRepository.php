<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[] Returns an array of User objects without the logged in User
     */
    public function findByExcept($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return User[] order by descending
     */
    public function getTheLatest()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Filter User By Name
     */
    public function filterUser($name, $email, $id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.name LIKE :name')
            ->andWhere('u.email LIKE :email')
            ->andWhere('u.id != :id')
            ->setParameter('name', $name."%")
            ->setParameter('email', $email."%")
            ->setParameter('id', $id)
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?User
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
