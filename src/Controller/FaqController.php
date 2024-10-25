<?php

namespace OHMedia\AccordionBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\AccordionBundle\Form\FaqType;
use OHMedia\AccordionBundle\Repository\FaqQuestionRepository;
use OHMedia\AccordionBundle\Repository\FaqRepository;
use OHMedia\AccordionBundle\Security\Voter\FaqQuestionVoter;
use OHMedia\AccordionBundle\Security\Voter\FaqVoter;
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
class FaqController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'faq_question_reorder';

    public function __construct(
        private Connection $connection,
        private FaqRepository $faqRepository,
        private FaqQuestionRepository $faqQuestionRepository,
        private Paginator $paginator,
        private RequestStack $requestStack
    ) {
    }

    #[Route('/faqs', name: 'faq_index', methods: ['GET'])]
    public function index(): Response
    {
        $newFaq = new Faq();

        $this->denyAccessUnlessGranted(
            FaqVoter::INDEX,
            $newFaq,
            'You cannot access the list of FAQs.'
        );

        $qb = $this->faqRepository->createQueryBuilder('f');
        $qb->orderBy('f.name', 'asc');

        return $this->render('@OHMediaAccordion/faq/faq_index.html.twig', [
            'pagination' => $this->paginator->paginate($qb, 20),
            'new_faq' => $newFaq,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/faq/create', name: 'faq_create', methods: ['GET', 'POST'])]
    public function create(): Response
    {
        $faq = new Faq();

        $this->denyAccessUnlessGranted(
            FaqVoter::CREATE,
            $faq,
            'You cannot create a new FAQ.'
        );

        $form = $this->createForm(FaqType::class, $faq);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->faqRepository->save($faq, true);

                $this->addFlash('notice', 'The FAQ was created successfully.');

                return $this->redirectToRoute('faq_view', [
                    'id' => $faq->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/faq/faq_create.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq,
        ]);
    }

    #[Route('/faq/{id}', name: 'faq_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] Faq $faq,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqVoter::VIEW,
            $faq,
            'You cannot view this FAQ.'
        );

        $newFaqQuestion = new FaqQuestion();
        $newFaqQuestion->setFaq($faq);

        return $this->render('@OHMediaAccordion/faq/faq_view.html.twig', [
            'faq' => $faq,
            'attributes' => $this->getAttributes(),
            'new_faq_question' => $newFaqQuestion,
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/faq/{id}/questions/reorder', name: 'faq_question_reorder_post', methods: ['POST'])]
    public function reorderPost(
        #[MapEntity(id: 'id')] Faq $faq,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqVoter::INDEX,
            $faq,
            'You cannot reorder the questions.'
        );

        $request = $this->requestStack->getCurrentRequest();

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $faqQuestions = $request->request->all('order');

        $this->connection->beginTransaction();

        try {
            foreach ($faqQuestions as $ordinal => $id) {
                $faqQuestion = $this->faqQuestionRepository->find($id);

                if ($faqQuestion) {
                    $faqQuestion->setOrdinal($ordinal);

                    $this->faqQuestionRepository->save($faqQuestion, true);
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/faq/{id}/edit', name: 'faq_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity(id: 'id')] Faq $faq,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqVoter::EDIT,
            $faq,
            'You cannot edit this FAQ.'
        );

        $form = $this->createForm(FaqType::class, $faq);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->faqRepository->save($faq, true);

                $this->addFlash('notice', 'The FAQ was updated successfully.');

                return $this->redirectToRoute('faq_view', [
                    'id' => $faq->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/faq/faq_edit.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq,
        ]);
    }

    #[Route('/faq/{id}/delete', name: 'faq_delete', methods: ['GET', 'POST'])]
    public function delete(
        #[MapEntity(id: 'id')] Faq $faq,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqVoter::DELETE,
            $faq,
            'You cannot delete this FAQ.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->faqRepository->remove($faq, true);

                $this->addFlash('notice', 'The FAQ was deleted successfully.');

                return $this->redirectToRoute('faq_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaAccordion/faq/faq_delete.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq,
        ]);
    }

    public static function getAttributes(): array
    {
        return [
            'faq' => [
                'view' => FaqVoter::VIEW,
                'create' => FaqVoter::CREATE,
                'delete' => FaqVoter::DELETE,
                'edit' => FaqVoter::EDIT,
            ],
            'faq_question' => [
                'create' => FaqQuestionVoter::CREATE,
                'delete' => FaqQuestionVoter::DELETE,
                'edit' => FaqQuestionVoter::EDIT,
            ],
        ];
    }
}
