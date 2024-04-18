<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class AccordionEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'FAQs';
    }

    public function getEntities(): array
    {
        return [Faq::class];
    }
}
