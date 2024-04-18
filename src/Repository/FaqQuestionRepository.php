<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\WysiwygBundle\Repository\WysiwygRepositoryInterface;

/**
 * @method FaqQuestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method FaqQuestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method FaqQuestion[]    findAll()
 * @method FaqQuestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqQuestionRepository extends ServiceEntityRepository implements WysiwygRepositoryInterface
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

    public function containsWysiwygShortcodes(string ...$shortcodes): bool
    {
        $ors = [];
        $params = new ArrayCollection();

        foreach ($shortcodes as $i => $shortcode) {
            $ors[] = 'fq.answer LIKE :shortcode_'.$i;
            $params[] = new Parameter('shortcode_'.$i, '%'.$shortcode.'%');
        }

        return $this->createQueryBuilder('fq')
            ->select('COUNT(fq)')
            ->where(implode(' OR ', $ors))
            ->setParameters($params)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}
