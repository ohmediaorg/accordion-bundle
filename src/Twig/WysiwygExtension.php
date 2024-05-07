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
            new TwigFunction('faq', [$this, 'faq'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function accordion(Environment $twig, int $id = null)
    {
        if (isset($this->accordions[$id])) {
            // prevent infinite recursion
            return;
        }

        $this->accordions[$id] = true;

        $accordion = $id ? $this->accordionRepository->find($id) : null;

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
        if (isset($this->faqs[$id])) {
            // prevent infinite recursion
            return;
        }

        $this->faqs[$id] = true;

        $faq = $this->faqRepository->find($id);

        if (!$faq) {
            return '';
        }

        $questions = $faq->getQuestions();

        if (!count($questions)) {
            return '';
        }

        $bootstrapAccordion = new BootstrapAccordion();

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($questions as $question) {
            $header = (string) $question->getQuestion();
            $body = $this->wysiwyg->render($question->getAnswer());

            $schema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $header,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $body,
                ],
            ];

            $bootstrapAccordionItem = new BootstrapAccordionItem($header, $body);

            $bootstrapAccordion->addItem($bootstrapAccordionItem);
        }

        $rendered = $twig->render('@OHMediaAccordion/faq.html.twig', [
            'accordion' => $bootstrapAccordion,
        ]);

        $rendered .= '<script type="application/ld+json">'.json_encode($schema).'</script>';

        return $rendered;
    }
}
