<?php

namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }


    public function findLastLog()
    {

        return $this->createQueryBuilder('l')
            ->andWhere('l.job = :val')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('val', "Docteur")
            ->getQuery()->getResult();

    }

    public function findByDatesAndJobs(\DateTime $start, \DateTime $end, string $job)
    {
        if ($job == '' or !isset($job))
            throw new \InvalidArgumentException("Le metier n'est pas définie correctement", 403);

        $interval = date_diff($start, $end);

        if ($interval->invert == 1) {
            $datetemp = $start;
            $start = $end;
            $end = $datetemp;
            unset($datetemp);
        }
        //Now we have check inputs let's get datas form DB.

        $liste = $this->createQueryBuilder('l')
            ->andWhere('l.job=:val')
            ->andWhere('l.debut >= :debut AND l.debut<=:fin')
            ->orderBy('l.id')
            ->setParameter('val', $job)
            ->setParameter('debut', $start->format("Y-m-d H:i:s"))
            ->setParameter('fin', $end->format("Y-m-d H:i:s"))
            ->getQuery()
            ->getResult();

        return $liste;
    }
}
