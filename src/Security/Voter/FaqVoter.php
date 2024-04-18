<?php

namespace OHMedia\AccordionBundle\Security\Voter;

use OHMedia\AccordionBundle\Entity\Faq;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class FaqVoter extends AbstractEntityVoter
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
        return Faq::class;
    }

    protected function canIndex(Faq $faq, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Faq $faq, User $loggedIn): bool
    {
        return true;
    }

    protected function canView(Faq $faq, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Faq $faq, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Faq $faq, User $loggedIn): bool
    {
        return true;
    }
}
