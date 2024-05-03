<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class AccordionEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Accordions';
    }

    public function getEntities(): array
    {
        return [Accordion::class, AccordionItem::class];
    }
}
