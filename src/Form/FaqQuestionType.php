<?php

namespace OHMedia\AccordionBundle\Form;

use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaqQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('question');

        $builder->add('answer', WysiwygType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FaqQuestion::class,
        ]);
    }
}
