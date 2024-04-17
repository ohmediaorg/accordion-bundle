<?php

namespace OHMedia\AccordionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\SecurityBundle\Entity\Traits\BlameableTrait;

#[ORM\Entity(repositoryClass: AccordionRepository::class)]
class Accordion
{
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __toString(): string
    {
        return 'Accordion #'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
