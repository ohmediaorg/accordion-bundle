<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Repository\FaqRepository;
use OHMedia\WysiwygBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\WysiwygBundle\Shortcodes\Shortcode;

class FaqShortcodeProvider extends AbstractShortcodeProvider
{
    public function __construct(private FaqRepository $faqRepository)
    {
    }

    public function getTitle(): string
    {
        return 'FAQs';
    }

    public function buildShortcodes(): void
    {
        $faqs = $this->faqRepository->createQueryBuilder('f')
            ->orderBy('f.name', 'asc')
            ->getQuery()
            ->getResult();

        foreach ($faqs as $faq) {
            $id = $faq->getId();

            $this->addShortcode(new Shortcode(
                sprintf('%s (ID:%s)', $faq, $id),
                'faq('.$id.')'
            ));
        }
    }
}
