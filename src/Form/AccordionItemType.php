<?php

namespace OHMedia\AccordionBundle\Form;

use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\WysiwygBundle\Form\Type\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccordionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $accordionItem = $options['data'];
        $accordion = $accordionItem->getAccordion();

        $builder->add('header', TextType::class, [
            'label' => $accordion->isFaq() ? 'Question' : 'Header',
        ]);

        $builder->add('content', WysiwygType::class, [
            'label' => $accordion->isFaq() ? 'Answer' : 'Content',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccordionItem::class,
        ]);
    }
}
