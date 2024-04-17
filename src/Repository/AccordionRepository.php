<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\Accordion;

/**
 * @method Accordion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accordion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accordion[]    findAll()
 * @method Accordion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accordion::class);
    }

    public function save(Accordion $accordion, bool $flush = false): void
    {
        $this->getEntityManager()->persist($accordion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Accordion $accordion, bool $flush = false): void
    {
        $this->getEntityManager()->remove($accordion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
