<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
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

    public function containsWysiwygShortcodes(string ...$shortcodes): bool
    {
        $ors = [];
        $params = new ArrayCollection();

        foreach ($shortcodes as $i => $shortcode) {
            $ors[] = 'ai.content LIKE :shortcode_'.$i;
            $params[] = new Parameter('shortcode_'.$i, '%'.$shortcode.'%');
        }

        return $this->createQueryBuilder('ai')
            ->select('COUNT(ai)')
            ->where(implode(' OR ', $ors))
            ->setParameters($params)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
