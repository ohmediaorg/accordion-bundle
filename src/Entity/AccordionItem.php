<?php

namespace OHMedia\AccordionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\AccordionBundle\Repository\AccordionItemRepository;
use OHMedia\SecurityBundle\Entity\Traits\BlameableTrait;

#[ORM\Entity(repositoryClass: AccordionItemRepository::class)]
class AccordionItem
{
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $ordinal = 9999;

    public function __toString(): string
    {
        return 'Accordion Item #'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(int $ordinal): self
    {
        $this->ordinal = $ordinal;

        return $this;
    }
}
