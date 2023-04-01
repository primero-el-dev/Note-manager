<?php

namespace App\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Ignore;
use App\Repository\NoteRepository;
use App\Entity\EntityInterface;
use App\Entity\User;
use App\Entity\AdditionalParameter;
use \DateTime;

#[ODM\HasLifecycleCallbacks]
#[ODM\Document(db: 'notemanager', collection: 'note', repositoryClass: NoteRepository::class)]
class Note implements EntityInterface
{
    #[ODM\Id]
    private ?string $id;

    #[Assert\NotBlank(message: 'Title is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Title must be at most {{limit}} characters long.')]
    #[ODM\Field(type: 'string')]
    private string $title = '';

    #[Assert\NotBlank(message: 'Content is required.')]
    #[Assert\Length(max: 4095, maxMessage: 'Content must be at most {{limit}} characters long.')]
    #[ODM\Field(type: 'string')]
    private string $content = '';

    #[Ignore]
    #[ODM\Field(type: 'string')]
    #[ODM\ReferenceOne(targetDocument: User::class, inversedBy: 'notes')]
    private ?User $user = null;

    #[ODM\Field(type: 'date')]
    private ?DateTime $createdAt = null;

    #[ODM\ReferenceMany(targetDocument: AdditionalParameter::class, mappedBy: 'note', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $additionalParameters;

    public function __construct()
    {
        $this->additionalParameters = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    #[ODM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime();

        return $this;
    }

    public function getAdditionalParameters(): Collection
    {
        return $this->additionalParameters;
    }

    public function setAdditionalParameters(Collection $additionalParameters): self
    {
        $this->additionalParameters = $additionalParameters;

        return $this;
    }

    public function addAdditionalParameter(AdditionalParameter $additionalParameter): self
    {
        if (!$this->additionalParameters->contains($additionalParameter)) {
            $this->additionalParameters->add($additionalParameter);
            $additionalParameter->setNote($this);
        }

        return $this;
    }

    public function removeAdditionalParameter(AdditionalParameter $additionalParameter): self
    {
        if ($this->additionalParameters->removeElement($additionalParameter)) {
            $additionalParameter->setNote(null);
        }

        return $this;
    }
}
