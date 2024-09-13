<?php

namespace OHMedia\AccordionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OHMedia\AccordionBundle\Repository\AccordionRepository;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccordionRepository::class)]
class Accordion
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    /**
     * @var Collection<int, AccordionItem>
     */
    #[ORM\OneToMany(targetEntity: AccordionItem::class, mappedBy: 'accordion', orphanRemoval: true)]
    #[ORM\OrderBy(['ordinal' => 'ASC'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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
