<?php

namespace OHMedia\AccordionBundle\Security\Voter;

use OHMedia\AccordionBundle\Entity\FaqQuestion;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class FaqQuestionVoter extends AbstractEntityVoter
{
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return FaqQuestion::class;
    }

    protected function canCreate(FaqQuestion $accordionQuestion, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(FaqQuestion $accordionQuestion, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(FaqQuestion $accordionQuestion, User $loggedIn): bool
    {
        return true;
    }
}
