<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('pen:read')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('pen:read')]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Pen::class)]
    private Collection $pens;

    public function __construct()
    {
        $this->pens = new ArrayCollection();
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

    /**
     * @return Collection<int, Pen>
     */
    public function getPens(): Collection
    {
        return $this->pens;
    }

    public function addPen(Pen $pen): static
    {
        if (!$this->pens->contains($pen)) {
            $this->pens->add($pen);
            $pen->setBrand($this);
        }

        return $this;
    }

    public function removePen(Pen $pen): static
    {
        if ($this->pens->removeElement($pen)) {
            // set the owning side to null (unless already changed)
            if ($pen->getBrand() === $this) {
                $pen->setBrand(null);
            }
        }

        return $this;
    }
}
