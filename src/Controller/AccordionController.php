<?php

namespace OHMedia\AccordionBundle\Controller;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\AccordionBundle\Form\AccordionType;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\AccordionBundle\Security\Voter\AccordionItemVoter;
use OHMedia\AccordionBundle\Security\Voter\AccordionVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AccordionController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'accordion_item_reorder';

    public function __construct(
        private AccordionRepository $accordionRepository,
        private AccordionItemRepository $accordionItemRepository,
        private Paginator $paginator,
        private RequestStack $requestStack
    ) {
    }

    #[Route('/accordions', name: 'accordion_index', methods: ['GET'])]
    public function indexAccordions(): Response
    {
        $newAccordion = (new Accordion())->setFaq(false);

        return $this->index($newAccordion);
    }

    #[Route('/faqs', name: 'faq_index', methods: ['GET'])]
    public function indexFaqs(): Response
    {
        $newAccordion = (new Accordion())->setFaq(true);

        return $this->index($newAccordion);
    }

    private function index(Accordion $newAccordion): Response
    {
        $nouns = $newAccordion->isFaq() ? 'FAQs' : 'accordions';

        $this->denyAccessUnlessGranted(
            AccordionVoter::INDEX,
            $newAccordion,
            "You cannot access the list of $nouns."
        );

        $qb = $this->accordionRepository->createQueryBuilder('a');
        $qb->where('a.faq = :faq');
        $qb->setParameter('faq', $newAccordion->isFaq());
        $qb->orderBy('a.id', 'desc');

        $template = $newAccordion->isFaq()
            ? '@OHMediaAccordion/faq/faq_index.html.twig'
            : '@OHMediaAccordion/accordion/accordion_index.html.twig';

        return $this->render($template, [
            'pagination' => $this->paginator->paginate($qb, 20),
            'new_accordion' => $newAccordion,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/accordion/create', name: 'accordion_create', methods: ['GET', 'POST'])]
    public function createAccordion(): Response
    {
        $accordion = (new Accordion())->setFaq(false);

        return $this->create($accordion);
    }

    #[Route('/faq/create', name: 'faq_create', methods: ['GET', 'POST'])]
    public function createFaq(): Response
    {
        $accordion = (new Accordion())->setFaq(true);

        return $this->create($accordion);
    }

    private function create(Accordion $accordion): Response
    {
        $noun = $accordion->isFaq() ? 'FAQ' : 'accordion';

        $this->denyAccessUnlessGranted(
            AccordionVoter::CREATE,
            $accordion,
            "You cannot create a new $noun."
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionRepository->save($accordion, true);

            $this->addFlash('notice', "The $noun was created successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_view' : 'accordion_view';

            return $this->redirectToRoute($redirectRoute, [
                'id' => $accordion->getId(),
            ]);
        }

        $template = $accordion->isFaq()
            ? '@OHMediaAccordion/faq/faq_create.html.twig'
            : '@OHMediaAccordion/accordion/accordion_create.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}', name: 'accordion_view', methods: ['GET'])]
    #[Route('/faq/{id}', name: 'faq_view', methods: ['GET'])]
    public function view(Accordion $accordion): Response
    {
        $noun = $accordion->isFaq() ? 'FAQ' : 'accordion';

        $this->denyAccessUnlessGranted(
            AccordionVoter::VIEW,
            $accordion,
            "You cannot view this $noun."
        );

        $template = $accordion->isFaq()
            ? '@OHMediaAccordion/faq/faq_view.html.twig'
            : '@OHMediaAccordion/accordion/accordion_view.html.twig';

        $newAccordionItem = new AccordionItem();
        $newAccordionItem->setAccordion($accordion);

        return $this->render($template, [
            'accordion' => $accordion,
            'attributes' => $this->getAttributes(),
            'new_accordion_item' => $newAccordionItem,
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/accordion/{id}/items/reorder', name: 'accordion_item_reorder_post', methods: ['POST'])]
    #[Route('/faq/{id}/question/reorder', name: 'faq_question_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        Accordion $accordion
    ): Response {
        $nouns = $accordion->isFaq() ? 'FAQ questions' : 'accordion items';

        $this->denyAccessUnlessGranted(
            AccordionVoter::INDEX,
            $accordion,
            "You cannot reorder the $nouns."
        );

        $request = $this->requestStack->getCurrentRequest();

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $accordionItems = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($accordionItems as $ordinal => $id) {
                $accordionItem = $this->accordionItemRepository->find($id);

                if ($accordionItem) {
                    $accordionItem->setOrdinal($ordinal);

                    $this->accordionItemRepository->save($accordionItem, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/accordion/{id}/edit', name: 'accordion_edit', methods: ['GET', 'POST'])]
    #[Route('/faq/{id}/edit', name: 'faq_edit', methods: ['GET', 'POST'])]
    public function edit(Accordion $accordion): Response
    {
        $noun = $accordion->isFaq() ? 'FAQ' : 'accordion';

        $this->denyAccessUnlessGranted(
            AccordionVoter::EDIT,
            $accordion,
            "You cannot edit this $noun."
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionRepository->save($accordion, true);

            $this->addFlash('notice', "The $noun was updated successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_view' : 'accordion_view';

            return $this->redirectToRoute($redirectRoute, [
                'id' => $accordion->getId(),
            ]);
        }

        $template = $accordion->isFaq()
            ? '@OHMediaAccordion/faq/faq_edit.html.twig'
            : '@OHMediaAccordion/accordion/accordion_edit.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}/delete', name: 'accordion_delete', methods: ['GET', 'POST'])]
    #[Route('/faq/{id}/delete', name: 'faq_delete', methods: ['GET', 'POST'])]
    public function delete(Accordion $accordion): Response
    {
        $noun = $accordion->isFaq() ? 'FAQ' : 'accordion';

        $this->denyAccessUnlessGranted(
            AccordionVoter::DELETE,
            $accordion,
            "You cannot delete this $noun."
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionRepository->remove($accordion, true);

            $this->addFlash('notice', "The $noun was deleted successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_index' : 'accordion_index';

            return $this->redirectToRoute($redirectRoute);
        }

        $template = $accordion->isFaq()
            ? '@OHMediaAccordion/faq/faq_delete.html.twig'
            : '@OHMediaAccordion/accordion/accordion_delete.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    public static function getAttributes(): array
    {
        return [
            'accordion' => [
                'view' => AccordionVoter::VIEW,
                'create' => AccordionVoter::CREATE,
                'delete' => AccordionVoter::DELETE,
                'edit' => AccordionVoter::EDIT,
            ],
            'accordion_item' => [
                'create' => AccordionItemVoter::CREATE,
                'delete' => AccordionItemVoter::DELETE,
                'edit' => AccordionItemVoter::EDIT,
            ],
        ];
    }
}
