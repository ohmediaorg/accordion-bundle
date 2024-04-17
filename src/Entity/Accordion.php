<?php

namespace OHMedia\AccordionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $faq = null;

    /**
     * @var Collection<int, AccordionItem>
     */
    #[ORM\OneToMany(targetEntity: AccordionItem::class, mappedBy: 'accordion', orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Accordion #'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isFaq(): ?bool
    {
        return $this->faq;
    }

    public function setFaq(bool $faq): static
    {
        $this->faq = $faq;

        return $this;
    }

    /**
     * @return Collection<int, AccordionItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(AccordionItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setAccordion($this);
        }

        return $this;
    }

    public function removeItem(AccordionItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getAccordion() === $this) {
                $item->setAccordion(null);
            }
        }

        return $this;
    }
}
