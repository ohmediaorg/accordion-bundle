<?php

namespace OHMedia\AccordionBundle\Service;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Security\Voter\AccordionVoter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class FaqNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(AccordionVoter::INDEX, (new Accordion())->setFaq(true))) {
            return (new NavLink('FAQs', 'faq_index'))
                ->setIcon('question-circle-fill');
        }

        return null;
    }
}
