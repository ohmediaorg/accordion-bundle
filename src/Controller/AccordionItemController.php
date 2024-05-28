<?php

namespace OHMedia\AccordionBundle\Controller;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\AccordionBundle\Form\AccordionItemType;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\AccordionBundle\Security\Voter\AccordionItemVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AccordionItemController extends AbstractController
{
    public function __construct(private AccordionItemRepository $accordionItemRepository)
    {
    }

    #[Route('/accordion/{id}/item/create', name: 'accordion_item_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Accordion $accordion,
    ): Response {
        $accordionItem = new AccordionItem();
        $accordionItem->setAccordion($accordion);

        $this->denyAccessUnlessGranted(
            AccordionItemVoter::CREATE,
            $accordionItem,
            'You cannot create a new item.'
        );

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionItemRepository->save($accordionItem, true);

            $this->addFlash('notice', 'The item was created successfully.');

            return $this->redirectToRoute('accordion_view', [
                'id' => $accordion->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/accordion/item/accordion_item_create.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/item/{id}/edit', name: 'accordion_item_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        AccordionItem $accordionItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::EDIT,
            $accordionItem,
            'You cannot edit this accordion item.'
        );

        $accordion = $accordionItem->getAccordion();

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionItemRepository->save($accordionItem, true);

            $this->addFlash('notice', 'The accordion item was updated successfully.');

            return $this->redirectToRoute('accordion_view', [
                'id' => $accordion->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/accordion/item/accordion_item_edit.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/item/{id}/delete', name: 'accordion_item_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        AccordionItem $accordionItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::DELETE,
            $accordionItem,
            'You cannot delete this accordion item.'
        );

        $accordion = $accordionItem->getAccordion();

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accordionItemRepository->remove($accordionItem, true);

            $this->addFlash('notice', 'The accordion item was deleted successfully.');

            return $this->redirectToRoute('accordion_view', [
                'id' => $accordion->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/accordion/item/accordion_item_delete.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }
}
