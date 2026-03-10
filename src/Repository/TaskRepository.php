<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    public function search(string $term,string $status,User $user): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.title LIKE :term')
            ->andWhere('t.owner = :user')
            ->setParameter('user',$user)
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('t.updatedAt','DESC');
            switch($status){
                case 'all':
                    break;

                case 'done':
                    $qb->andWhere('t.status = :val')
                    ->setParameter('val',1);
                    break;
                
                case 'undone':
                    $qb->andWhere('t.status = :val')
                    ->setParameter('val',0);
                    break;
            }
        $query = $qb->getQuery();
                    
        return $query->execute();
    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
