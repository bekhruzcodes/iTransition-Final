<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "questions")]
#[ORM\HasLifecycleCallbacks]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Template::class, inversedBy: "questions")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Template $template = null;

    #[ORM\OneToMany(targetEntity: QuestionOption::class, mappedBy: "question", cascade: ["persist", "remove"])]
    private Collection $options;

    #[ORM\Column(type: "string", length: 255)]
    private string $text;

    #[ORM\Column(type: "string", length: 50)]
    private string $type;

    #[ORM\Column(type: "boolean", options: ["default" => true])]
    private bool $required = true;

    #[ORM\Column(type: "integer")]
    private int $orderNum;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $updatedAt;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->options = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function __construct()
    {
        // Initialize the options collection in constructor
        $this->options = new ArrayCollection();
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    public function getOrderNum(): int
    {
        return $this->orderNum;
    }

    public function setOrderNum(int $orderNum): self
    {
        $this->orderNum = $orderNum;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(QuestionOption $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setQuestion($this);
        }
        return $this;
    }

    public function removeOption(QuestionOption $option): self
    {
        if ($this->options->removeElement($option)) {
            if ($option->getQuestion() === $this) {
                $option->setQuestion(null);
            }
        }
        return $this;
    }
}
