<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\BackendBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\BackendBundle\Shortcodes\Shortcode;

class AccordionShortcodeProvider extends AbstractShortcodeProvider
{
    public function __construct(private AccordionRepository $accordionRepository)
    {
    }

    public function getTitle(): string
    {
        return 'Accordions';
    }

    public function buildShortcodes(): void
    {
        $accordions = $this->accordionRepository->createQueryBuilder('a')
            ->orderBy('a.name', 'asc')
            ->getQuery()
            ->getResult();

        foreach ($accordions as $accordion) {
            $id = $accordion->getId();

            $this->addShortcode(new Shortcode(
                sprintf('%s (ID:%s)', $accordion, $id),
                'accordion('.$id.')'
            ));
        }
    }
}
