<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\AccordionItem;

/**
 * @method AccordionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccordionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccordionItem[]    findAll()
 * @method AccordionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordionItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccordionItem::class);
    }

    public function save(AccordionItem $accordionItem, bool $flush = false): void
    {
        $this->getEntityManager()->persist($accordionItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccordionItem $accordionItem, bool $flush = false): void
    {
        $this->getEntityManager()->remove($accordionItem);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}