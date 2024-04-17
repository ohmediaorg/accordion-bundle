<?php

namespace OHMedia\AccordionBundle\Security\Voter;

use OHMedia\AccordionBundle\Entity\AccordionItem;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class AccordionItemVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return AccordionItem::class;
    }

    protected function canIndex(AccordionItem $accordionItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(AccordionItem $accordionItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(AccordionItem $accordionItem, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(AccordionItem $accordionItem, User $loggedIn): bool
    {
        return true;
    }
}
