<?php

namespace App\Security\Voter;

use App\Entity\Accordion;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class AccordionVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Accordion::class;
    }

    protected function canIndex(Accordion $accordion, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Accordion $accordion, User $loggedIn): bool
    {
        return true;
    }

    protected function canView(Accordion $accordion, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Accordion $accordion, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Accordion $accordion, User $loggedIn): bool
    {
        return true;
    }
}
