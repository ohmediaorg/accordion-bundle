<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\Faq;

/**
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findAll()
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    public function save(Faq $accordion, bool $flush = false): void
    {
        $this->getEntityManager()->persist($accordion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Faq $accordion, bool $flush = false): void
    {
        $this->getEntityManager()->remove($accordion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
