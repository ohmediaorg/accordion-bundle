<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\FaqQuestion;

/**
 * @method FaqQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaqQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaqQuestion[]    findAll()
 * @method FaqQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FaqQuestion::class);
    }

    public function save(FaqQuestion $accordionQuestion, bool $flush = false): void
    {
        $this->getEntityManager()->persist($accordionQuestion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FaqQuestion $accordionQuestion, bool $flush = false): void
    {
        $this->getEntityManager()->remove($accordionQuestion);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
