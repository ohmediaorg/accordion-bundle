<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class AccordionEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Accordions & FAQs';
    }

    public function getEntities(): array
    {
        return [Accordion::class];
    }
}
