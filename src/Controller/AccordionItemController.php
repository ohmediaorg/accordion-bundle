<?php

namespace App\Controller\Backend;

use App\Entity\AccordionItem;
use App\Form\AccordionItemType;
use App\Repository\AccordionItemRepository;
use App\Security\Voter\AccordionItemVoter;
use Doctrine\DBAL\Connection;
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
    private const CSRF_TOKEN_REORDER = 'accordion_item_reorder';

    #[Route('/accordion-items', name: 'accordion_item_index', methods: ['GET'])]
    public function index(AccordionItemRepository $accordionItemRepository): Response
    {
        $newAccordionItem = new AccordionItem();

        $this->denyAccessUnlessGranted(
            AccordionItemVoter::INDEX,
            $newAccordionItem,
            'You cannot access the list of accordion items.'
        );

        $accordionItems = $accordionItemRepository->createQueryBuilder('f')
            ->orderBy('f.ordinal', 'asc')
            ->getQuery()
            ->getResult();

        return $this->render('@backend/accordion_item/accordion_item_index.html.twig', [
            'accordion_items' => $accordionItems,
            'new_accordion_item' => $newAccordionItem,
            'attributes' => $this->getAttributes(),
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/accordion-items/reorder', name: 'accordion_item_reorder_post', methods: ['POST'])]
    public function reorderPost(
        Connection $connection,
        AccordionItemRepository $accordionItemRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionItemVoter::INDEX,
            new AccordionItem(),
            'You cannot reorder the accordion items.'
        );

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $accordionItems = $request->request->all('order');

        $connection->beginTransaction();

        try {
            foreach ($accordionItems as $ordinal => $id) {
                $accordionItem = $accordionItemRepository->find($id);

                if ($accordionItem) {
                    $accordionItem->setOrdinal($ordinal);

                    $accordionItemRepository->save($accordionItem, true);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/accordion-item/create', name: 'accordion_item_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        AccordionItemRepository $accordionItemRepository
    ): Response {
        $accordionItem = new AccordionItem();

        $this->denyAccessUnlessGranted(
            AccordionItemVoter::CREATE,
            $accordionItem,
            'You cannot create a new accordion item.'
        );

        $form = $this->createForm(AccordionItemType::class, $accordionItem);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accordionItemRepository->save($accordionItem, true);

            $this->addFlash('notice', 'The accordion item was created successfully.');

            return $this->redirectToRoute('accordion_item_index');
        }

        return $this->render('@backend/accordion_item/accordion_item_create.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
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

        return $this->render('@backend/accordion_item/accordion_item_edit.html.twig', [
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

        return $this->render('@backend/accordion_item/accordion_item_delete.html.twig', [
            'form' => $form->createView(),
            'accordion_item' => $accordionItem,
        ]);
    }

    private function getAttributes(): array
    {
        return [
            'create' => AccordionItemVoter::CREATE,
            'delete' => AccordionItemVoter::DELETE,
            'edit' => AccordionItemVoter::EDIT,
        ];
    }
}
