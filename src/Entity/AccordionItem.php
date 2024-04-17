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

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Accordion $accordion = null;

    #[ORM\Column(length: 255)]
    private ?string $header = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

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

    public function getAccordion(): ?Accordion
    {
        return $this->accordion;
    }

    public function setAccordion(?Accordion $accordion): static
    {
        $this->accordion = $accordion;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(string $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
