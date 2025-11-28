<?php

namespace OHMedia\AccordionBundle\Controller;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\AccordionBundle\Form\AccordionItemType;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\AccordionBundle\Security\Voter\AccordionItemVoter;
use OHMedia\BackendBundle\Form\MultiSaveType;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
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
        #[MapEntity(id: 'id')] Accordion $accordion,
    ): Response {
        $accordionItem = new AccordionItem();
        $accordionItem->setAccordion($accordion);

        $this->denyAccessUnlessGranted(
            AccordionItemVoter::CREATE,
            $accordionItem,
            'You cannot create a new item.'
        );

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionItemRepository->save($accordionItem, true);

                $this->addFlash('notice', 'The item was created successfully.');

                return $this->redirectForm($accordionItem, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
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
        #[MapEntity(id: 'id')] AccordionItem $accordionItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::EDIT,
            $accordionItem,
            'You cannot edit this item.'
        );

        $accordion = $accordionItem->getAccordion();

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('save', MultiSaveType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionItemRepository->save($accordionItem, true);

                $this->addFlash('notice', 'The item was updated successfully.');

                return $this->redirectForm($accordionItem, $form);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/accordion/item/accordion_item_edit.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }

    private function redirectForm(AccordionItem $accordionItem, FormInterface $form): Response
    {
        $clickedButtonName = $form->getClickedButton()->getName() ?? null;

        if ('keep_editing' === $clickedButtonName) {
            return $this->redirectToRoute('accordion_item_edit', [
                'id' => $accordionItem->getId(),
            ]);
        } elseif ('add_another' === $clickedButtonName) {
            return $this->redirectToRoute('accordion_item_create', [
                'id' => $accordionItem->getAccordion()->getId(),
            ]);
        } else {
            return $this->redirectToRoute('accordion_view', [
                'id' => $accordionItem->getAccordion()->getId(),
            ]);
        }
    }

    #[Route('/accordion/item/{id}/delete', name: 'accordion_item_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        #[MapEntity(id: 'id')] AccordionItem $accordionItem,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::DELETE,
            $accordionItem,
            'You cannot delete this item.'
        );

        $accordion = $accordionItem->getAccordion();

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionItemRepository->remove($accordionItem, true);

                $this->addFlash('notice', 'The item was deleted successfully.');

                return $this->redirectToRoute('accordion_view', [
                    'id' => $accordion->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/accordion/item/accordion_item_delete.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }
}
