<?php

namespace OHMedia\AccordionBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\AccordionBundle\Form\AccordionType;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\AccordionBundle\Security\Voter\AccordionItemVoter;
use OHMedia\AccordionBundle\Security\Voter\AccordionVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private Connection $connection,
        private Paginator $paginator,
        private RequestStack $requestStack
    ) {
    }

    #[Route('/accordions', name: 'accordion_index', methods: ['GET'])]
    public function index(): Response
    {
        $newAccordion = new Accordion();

        $this->denyAccessUnlessGranted(
            AccordionVoter::INDEX,
            $newAccordion,
            'You cannot access the list of accordions.'
        );

        $qb = $this->accordionRepository->createQueryBuilder('a');
        $qb->orderBy('a.name', 'asc');

        return $this->render('@OHMediaAccordion/accordion/accordion_index.html.twig', [
            'pagination' => $this->paginator->paginate($qb, 20),
            'new_accordion' => $newAccordion,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/accordion/create', name: 'accordion_create', methods: ['GET', 'POST'])]
    public function create(): Response
    {
        $accordion = new Accordion();

        $this->denyAccessUnlessGranted(
            AccordionVoter::CREATE,
            $accordion,
            'You cannot create a new accordion.'
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionRepository->save($accordion, true);

                $this->addFlash('notice', 'The accordion was created successfully.');

                return $this->redirectToRoute('accordion_view', [
                    'id' => $accordion->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/accordion/accordion_create.html.twig', [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}', name: 'accordion_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] Accordion $accordion,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::VIEW,
            $accordion,
            'You cannot view this accordion.'
        );

        $newAccordionItem = new AccordionItem();
        $newAccordionItem->setAccordion($accordion);

        return $this->render('@OHMediaAccordion/accordion/accordion_view.html.twig', [
            'accordion' => $accordion,
            'attributes' => $this->getAttributes(),
            'new_accordion_item' => $newAccordionItem,
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/accordion/{id}/items/reorder', name: 'accordion_item_reorder_post', methods: ['POST'])]
    public function reorderPost(
        #[MapEntity(id: 'id')] Accordion $accordion,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::INDEX,
            $accordion,
            'You cannot reorder the items.'
        );

        $request = $this->requestStack->getCurrentRequest();

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $accordionItems = $request->request->all('order');

        $this->connection->beginTransaction();

        try {
            foreach ($accordionItems as $ordinal => $id) {
                $accordionItem = $this->accordionItemRepository->find($id);

                if ($accordionItem) {
                    $accordionItem->setOrdinal($ordinal);

                    $this->accordionItemRepository->save($accordionItem, true);
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/accordion/{id}/edit', name: 'accordion_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity(id: 'id')] Accordion $accordion,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::EDIT,
            $accordion,
            'You cannot edit this accordion.'
        );

        $form = $this->createForm(AccordionType::class, $accordion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionRepository->save($accordion, true);

                $this->addFlash('notice', 'The accordion was updated successfully.');

                return $this->redirectToRoute('accordion_view', [
                    'id' => $accordion->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/accordion/accordion_edit.html.twig', [
            'form' => $form->createView(),
            'accordion' => $accordion,
        ]);
    }

    #[Route('/accordion/{id}/delete', name: 'accordion_delete', methods: ['GET', 'POST'])]
    public function delete(
        #[MapEntity(id: 'id')] Accordion $accordion,
    ): Response {
        $this->denyAccessUnlessGranted(
            AccordionVoter::DELETE,
            $accordion,
            'You cannot delete this accordion.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->accordionRepository->remove($accordion, true);

                $this->addFlash('notice', 'The accordion was deleted successfully.');

                return $this->redirectToRoute('accordion_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/accordion/accordion_delete.html.twig', [
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
