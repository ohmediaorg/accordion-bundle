<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class FaqEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'FAQs';
    }

    public function getEntities(): array
    {
        return [Faq::class, FaqQuestion::class];
    }
}
