<?php

namespace OHMedia\AccordionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function getShortcodeQueryBuilder(string $shortcode): QueryBuilder
    {
        return $this->createQueryBuilder('fq')
            ->where('fq.answer LIKE :shortcode')
            ->setParameter('shortcode', '%'.$shortcode.'%');
    }

    public function getShortcodeRoute(): string
    {
        return 'faq_question_edit';
    }

    public function getShortcodeRouteParams(mixed $entity): array
    {
        return ['id' => $entity->getId()];
    }

    public function getShortcodeHeading(): string
    {
        return 'FAQs';
    }

    public function getShortcodeLinkText(mixed $entity): string
    {
        return sprintf(
            '%s - FAQ Question (ID:%s)',
            (string) $entity->getFaq(),
            $entity->getId(),
        );
    }
}
