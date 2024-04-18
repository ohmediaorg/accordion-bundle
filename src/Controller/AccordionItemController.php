<?php

namespace OHMedia\AccordionBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\AccordionBundle\Form\AccordionItemType;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\AccordionBundle\Security\Voter\AccordionItemVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class AccordionItemController extends AbstractController
{
    #[Route('/accordion/{id}/item/create', name: 'accordion_item_create', methods: ['GET', 'POST'])]
    #[Route('/faq/{id}/question/create', name: 'faq_question_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Accordion $accordion,
        AccordionItemRepository $accordionItemRepository
    ): Response {
        $accordionItem = new AccordionItem();
        $accordionItem->setAccordion($accordion);

        $noun = $accordion->isFaq() ? 'FAQ question' : 'accordion item';

        $this->denyAccessUnlessGranted(
            AccordionItemVoter::CREATE,
            $accordionItem,
            "You cannot create a new $noun."
        );

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionItemRepository->save($accordionItem, true);

            $this->addFlash('notice', "The $noun was created successfully.");

            $redirectRoute = $accordion->isFaq() ? 'faq_question_index' : 'accordion_item_index';

            return $this->redirectToRout e($redirectRoute, [
                'id' => $accordion->getId(),
            ]);
        }

        $template = $accordion->isFaq()
            ? '@OHMediaAccordion/faq/question/faq_question_create.html.twig'
            : '@OHMediaAccordion/accordion/item/accordion_item_create.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion-item/{id}/edit', name: 'accordion_item_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        AccordionItem $accordionItem,
        AccordionItemRepository $accordionItemRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::EDIT,
            $accordionItem,
            'You cannot edit this accordion item.'
        );

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionItemRepository->save($accordionItem, true);

            $this->addFlash('notice', 'The accordion item was updated successfully.');

            return $this->redirectToRoute('accordion_item_index');
        }

        return $this->render('@OHMediaAccordion/accordion_item/accordion_item_edit.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
        ]);
    }

    #[Route('/accordion-item/{id}/delete', name: 'accordion_item_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        AccordionItem $accordionItem,
        AccordionItemRepository $accordionItemRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::DELETE,
            $accordionItem,
            'You cannot delete this accordion item.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionItemRepository->remove($accordionItem, true);

            $this->addFlash('notice', 'The accordion item was deleted successfully.');

            return $this->redirectToRoute('accordion_item_index');
        }

        return $this->render('@OHMediaAccordion/accordion_item/accordion_item_delete.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
        ]);
    }
}
