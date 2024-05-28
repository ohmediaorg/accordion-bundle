<?php

namespace OHMedia\AccordionBundle\Controller;

use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\AccordionBundle\Form\FaqQuestionType;
use OHMedia\AccordionBundle\Repository\FaqQuestionRepository;
use OHMedia\AccordionBundle\Security\Voter\FaqQuestionVoter;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class FaqQuestionController extends AbstractController
{
    public function __construct(private FaqQuestionRepository $faqQuestionRepository)
    {
    }

    #[Route('/faq/{id}/question/create', name: 'faq_question_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Faq $faq,
    ): Response {
        $faqQuestion = new FaqQuestion();
        $faqQuestion->setFaq($faq);

        $this->denyAccessUnlessGranted(
            FaqQuestionVoter::CREATE,
            $faqQuestion,
            'You cannot create a new question.'
        );

        $form = $this->createForm(FaqQuestionType::class, $faqQuestion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->faqQuestionRepository->save($faqQuestion, true);

            $this->addFlash('notice', 'The question was created successfully.');

            return $this->redirectToRoute('faq_view', [
                'id' => $faq->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/faq/question/faq_question_create.html.twig', [
            'form' => $form->createView(),
            'faq_question' => $faqQuestion,
            'faq' => $faq,
        ]);
    }

    #[Route('/faq/question/{id}/edit', name: 'faq_question_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        FaqQuestion $faqQuestion,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqQuestionVoter::EDIT,
            $faqQuestion,
            'You cannot edit this question.'
        );

        $faq = $faqQuestion->getFaq();

        $form = $this->createForm(FaqQuestionType::class, $faqQuestion);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->faqQuestionRepository->save($faqQuestion, true);

            $this->addFlash('notice', 'The question was updated successfully.');

            return $this->redirectToRoute('faq_view', [
                'id' => $faq->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/faq/question/faq_question_edit.html.twig', [
            'form' => $form->createView(),
            'faq_question' => $faqQuestion,
            'faq' => $faq,
        ]);
    }

    #[Route('/faq/question/{id}/delete', name: 'faq_question_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        FaqQuestion $faqQuestion,
    ): Response {
        $this->denyAccessUnlessGranted(
            FaqQuestionVoter::DELETE,
            $faqQuestion,
            'You cannot delete this question.'
        );

        $faq = $faqQuestion->getFaq();

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->faqQuestionRepository->remove($faqQuestion, true);

            $this->addFlash('notice', 'The question was deleted successfully.');

            return $this->redirectToRoute('faq_view', [
                'id' => $faq->getId(),
            ]);
        }

        return $this->render('@OHMediaAccordion/faq/question/faq_question_delete.html.twig', [
            'form' => $form->createView(),
            'faq_question' => $faqQuestion,
            'faq' => $faq,
        ]);
    }
}
