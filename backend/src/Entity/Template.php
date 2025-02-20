<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "templates")]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)] // Set nullable: false to match DB
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Topic")]
    #[ORM\JoinColumn(name: "topic_id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)] // Set nullable: false to match DB
    private ?Topic $topic = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: "templates")]
    private Collection $tags;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: "boolean")]
    private bool $isPublic = true; // Default value matches DB

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $createdAt = null; // You can set a default value in DB, but make sure it's nullable in the entity.

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'template')]
    private Collection $questions; // Same for updated_at

    public function __construct()
    {
        $this->createdAt = new \DateTime(); // Default value
        $this->updatedAt = new \DateTime(); // Default value
        $this->questions = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function isIsPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setTemplate($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getTemplate() === $this) {
                $question->setTemplate(null);
            }
        }

        return $this;
    }

    public function getForms()
    {
        return ["Needs to be implemented"];
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }
}
