<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
#[ORM\Table(name: "tags")]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['template-read', 'template-write'])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Groups(['template-read', 'template-write'])]
    private ?string $name = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, Template>
     */
    #[ORM\ManyToMany(targetEntity: Template::class, inversedBy: "tags")]
    #[ORM\JoinTable(name: "templates_tags")]
    #[Groups(['tag-read'])]
    private Collection $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Template>
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function addTemplate(Template $template): self
    {
        if (!$this->templates->contains($template)) {
            $this->templates->add($template);
            $template->addTag($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->templates->removeElement($template)) {
            $template->removeTag($this);
        }

        return $this;
    }
}