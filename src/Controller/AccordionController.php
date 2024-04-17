<?php

namespace App\Controller\Backend;

use App\Entity\Accordion;
use App\Form\AccordionType;
use App\Repository\AccordionRepository;
use App\Security\Voter\AccordionVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AccordionController extends AbstractController
{
    #[Route('/accordions', name: 'accordion_index', methods: ['GET'])]
    public function index(
        AccordionRepository $accordionRepository,
        Paginator $paginator
    ): Response {
        $newAccordion = new Accordion();

        $this->denyAccessUnlessGranted(
            AccordionVoter::INDEX,
            $newAccordion,
            'You cannot access the list of accordions.'
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
    public function create(
        Request $request,
        AccordionRepository $accordionRepository
    ): Response {
        $accordion = new Accordion();

        $this->denyAccessUnlessGranted(
            AccordionVoter::CREATE,
            $accordion,
            'You cannot create a new accordion.'
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionRepository->save($accordion, true);

            $this->addFlash('notice', 'The accordion was created successfully.');

            return $this->redirectToRoute('accordion_index');
        }

        return $this->render('@backend/accordion/accordion_create.html.twig', [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}', name: 'accordion_view', methods: ['GET'])]
    public function view(Accordion $accordion): Response
    {
        $this->denyAccessUnlessGranted(
            AccordionVoter::VIEW,
            $accordion,
            'You cannot view this accordion.'
        );

        return $this->render('@backend/accordion/accordion_view.html.twig', [
            'accordion' => $accordion,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/accordion/{id}/edit', name: 'accordion_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Accordion $accordion,
        AccordionRepository $accordionRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::EDIT,
            $accordion,
            'You cannot edit this accordion.'
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionRepository->save($accordion, true);

            $this->addFlash('notice', 'The accordion was updated successfully.');
            return $this->redirectToRoute('accordion_view', [
                'id' => $accordion->getId(),
            ]);
        }

        return $this->render('@backend/accordion/accordion_edit.html.twig', [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}/delete', name: 'accordion_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Accordion $accordion,
        AccordionRepository $accordionRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::DELETE,
            $accordion,
            'You cannot delete this accordion.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionRepository->remove($accordion, true);

            $this->addFlash('notice', 'The accordion was deleted successfully.');

            return $this->redirectToRoute('accordion_index');
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
