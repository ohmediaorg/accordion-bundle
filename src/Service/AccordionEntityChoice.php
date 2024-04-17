<?php

namespace App\Service\EntityChoice;

use App\Entity\Accordion;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class AccordionEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Accordions';
    }

    public function getEntities(): array
    {
        return [Accordion::class];
    }
}
