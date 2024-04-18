<?php

namespace OHMedia\AccordionBundle\Twig;

use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\AccordionBundle\Repository\FaqRepository;
use OHMedia\BootstrapBundle\Component\Accordion\Accordion as BootstrapAccordion;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Twig\Environment;
use Twig\TwigFunction;

class WysiwygExtension extends AbstractWysiwygExtension
{
    public function __construct(
        private AccordionRepository $accordionRepository,
        private FaqRepository $faqRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('accordion', [$this, 'accordion'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
            new TwigFunction('faq', [$this, 'accordion'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function accordion(Environment $twig, int $id = null)
    {
        $accordion = $this->accordionRepository->find($id);

        if (!$accordion) {
            return '';
        }

        $items = $accordion->getItems();

        if (!count($items)) {
            return '';
        }

        $bootstrapAccordion = new BootstrapAccordion();
        $bootstrapAccordion->setItems(...$items);

        return $twig->render('@OHMediaAccordion/accordion.html.twig', [
            'accordion' => $bootstrapAccordion,
        ]);
    }

    public function faq(Environment $twig, int $id = null)
    {
        $faq = $this->faqRepository->find($id);

        if (!$faq) {
            return '';
        }

        $questions = $faq->getQuestions();

        if (!count($questions)) {
            return '';
        }

        $bootstrapAccordion = new BootstrapAccordion();
        $bootstrapAccordion->setItems(...$questions);

        return $twig->render('@OHMediaAccordion/faq.html.twig', [
            'accordion' => $bootstrapAccordion,
        ]);
    }
}
