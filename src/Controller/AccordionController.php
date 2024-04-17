<?php

namespace OHMedia\AccordionBundle\Controller\Backend;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Form\AccordionType;
use OHMedia\AccordionBundle\Repository\AccordionRepository;
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
    public function __construct(
        private AccordionRepository $accordionRepository,
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

        $qb = $accordionRepository->createQueryBuilder('a');
        $qb->orderBy('a.id', 'desc');

        return $this->render('@backend/accordion/accordion_index.html.twig', [
            'pagination' => $paginator->paginate($qb, 20),
            'new_accordion' => $newAccordion,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/accordion/create', name: 'accordion_create', methods: ['GET', 'POST'])]
    public function createAccordion(): Response
    {
        $newAccordion = (new Accordion())->setFaq(false);

        return $this->create($newAccordion);
    }

    #[Route('/faq/create', name: 'faq_create', methods: ['GET', 'POST'])]
    public function faqs(): Response
    {
        $newAccordion = (new Accordion())->setFaq(true);

        return $this->create($newAccordion);
    }

    private function create(Accordion $accordion): Response
    {
        $noun = $newAccordion->isFaq() ? 'FAQ' : 'accordion';

        $this->denyAccessUnlessGranted(
            AccordionVoter::CREATE,
            $accordion,
            "You cannot create a new $noun."
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionRepository->save($accordion, true);

            $this->addFlash('notice', "The $noun was created successfully.");

            return $this->redirectToRoute('accordion_index');
        }

        return $this->render('@backend/accordion/accordion_create.html.twig', [
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

        return $this->render('@backend/accordion/accordion_view.html.twig', [
            'accordion' => $accordion,
            'attributes' => $this->getAttributes(),
        ]);
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
            $accordionRepository->save($accordion, true);

            $this->addFlash('notice', "The $noun was updated successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_view' : 'accordion_view';

            return $this->redirectToRoute($redirectRoute, [
                'id' => $accordion->getId(),
            ]);
        }

        return $this->render('@backend/accordion/accordion_edit.html.twig', [
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
            $accordionRepository->remove($accordion, true);

            $this->addFlash('notice', "The $noun was deleted successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_index' : 'accordion_index';

            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render('@backend/accordion/accordion_delete.html.twig', [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'view' => AccordionVoter::VIEW,
            'create' => AccordionVoter::CREATE,
            'delete' => AccordionVoter::DELETE,
            'edit' => AccordionVoter::EDIT,
        ];
    }
}
