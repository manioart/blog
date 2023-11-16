<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllPosts(int $page) {
        $dbquery = $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')
        ->addSelect('u')
        ->getQuery()
        ->getResult();

        return $this->paginator->paginate($dbquery, $page, 3);
    }

    public function findAllUserPosts(int $page,$userId) {
        $dbquery = $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')
        ->addSelect('u')
        ->where('p.user = :id')
        ->setParameter('id', $userId)
        ->getQuery()
        ->getResult();

        return $this->paginator->paginate($dbquery, $page, 3);
    }

    public function isLiked($authUser, $postId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('p.user = :authUser')
            ->innerJoin('p.usersThatLike', 'usersThatLike')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
             ->getResult();
    }

    public function isDisliked($authUser, $postId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :postId')
            ->andWhere('p.user = :authUser')
            ->innerJoin('p.usersThatDontLike', 'usersThatDontLike')
            ->setParameter('authUser', $authUser)
            ->setParameter('postId', $postId)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
