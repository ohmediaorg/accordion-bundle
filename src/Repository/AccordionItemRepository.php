<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\WysiwygBundle\Repository\WysiwygRepositoryInterface;

/**
 * @method AccordionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccordionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccordionItem[]    findAll()
 * @method AccordionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordionItemRepository extends ServiceEntityRepository implements WysiwygRepositoryInterface
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

    public function getShortcodeQueryBuilder(string $shortcode): QueryBuilder
    {
        return $this->createQueryBuilder('ai')
            ->where('ai.content LIKE :shortcode')
            ->setParameter('shortcode', '%'.$shortcode.'%');
    }

    public function getEntityRoute(): string
    {
        return 'accordion_item_edit';
    }

    public function getEntityRouteParams(mixed $entity): array
    {
        return ['id' => $entity->getId()];
    }

    public function getEntityName(): string
    {
        return 'Accordion Item';
    }
}
