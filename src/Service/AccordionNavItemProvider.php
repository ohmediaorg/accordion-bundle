<?php

namespace OHMedia\AccordionBundle\Service\Backend\Nav;

use OHMedia\AccordionBundle\Entity\Accordion;
use OHMedia\AccordionBundle\Security\Voter\AccordionVoter;
use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;

class AccordionNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(AccordionVoter::INDEX, new Accordion())) {
            return (new NavLink('Accordions', 'accordion_index'))
                ->setIcon('arrow-down-square-fill');
        }

        return null;
    }
}
