<?php

namespace OHMedia\AccordionBundle\Twig;

use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\AccordionBundle\Repository\FaqRepository;
use OHMedia\BootstrapBundle\Component\Accordion\Accordion as BootstrapAccordion;
use OHMedia\BootstrapBundle\Component\Accordion\AccordionItem as BootstrapAccordionItem;
use OHMedia\WysiwygBundle\Service\Wysiwyg;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Twig\Environment;
use Twig\TwigFunction;

class WysiwygExtension extends AbstractWysiwygExtension
{
    private array $accordions = [];
    private array $faqs = [];

    public function __construct(
        private AccordionRepository $accordionRepository,
        private FaqRepository $faqRepository,
        private Wysiwyg $wysiwyg,
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
        if (!isset($this->accordions[$id])) {
            $this->accordions[$id] = 0;
        }

        if ($this->accordions[$id] > 5) {
            // prevent infinite recursion
            return '';
        }

        ++$this->accordions[$id];

        $accordion = $this->accordionRepository->find($id);

        if (!$accordion) {
            return '';
        }

        $items = $accordion->getItems();

        if (!count($items)) {
            return '';
        }

        $bootstrapAccordion = new BootstrapAccordion();

        foreach ($items as $item) {
            $header = (string) $item->getHeader();
            $body = $this->wysiwyg->render($item->getContent());

            $bootstrapAccordionItem = new BootstrapAccordionItem($header, $body);

            $bootstrapAccordion->addItem($bootstrapAccordionItem);
        }

        return $twig->render('@OHMediaAccordion/accordion.html.twig', [
            'accordion' => $bootstrapAccordion,
        ]);
    }

    public function faq(Environment $twig, int $id = null)
    {
        if (!isset($this->faqs[$id])) {
            $this->faqs[$id] = 0;
        }

        if ($this->faqs[$id] > 5) {
            // prevent infinite recursion
            return '';
        }

        ++$this->faqs[$id];

        $faq = $this->faqRepository->find($id);

        if (!$faq) {
            return '';
        }

        $questions = $faq->getQuestions();

        if (!count($questions)) {
            return '';
        }

        $bootstrapAccordion = new BootstrapAccordion();

        foreach ($questions as $question) {
            $header = (string) $question->getQuestion();
            $body = $this->wysiwyg->render($question->getAnswer());

            $bootstrapAccordionItem = new BootstrapAccordionItem($header, $body);

            $bootstrapAccordion->addItem($bootstrapAccordionItem);
        }

        return $twig->render('@OHMediaAccordion/faq.html.twig', [
            'accordion' => $bootstrapAccordion,
        ]);
    }
}
