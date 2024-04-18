<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\AccordionBundle\Security\Voter\FaqVoter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class FaqNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(FaqVoter::INDEX, new Faq())) {
            return (new NavLink('FAQs', 'faq_index'))
                ->setIcon('question-circle-fill');
        }

        return null;
    }
}
