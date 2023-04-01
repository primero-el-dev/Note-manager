<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\PersistentCollection;
use App\Repository\UserRepository;
use App\Entity\EntityInterface;
use App\Entity\Note;
use DateTime;

#[Unique(fields: ['email'], errorPath: 'email', message: 'Given email already exists in our database. Please choose another.')]
#[ODM\HasLifecycleCallbacks]
#[ODM\Document(db: 'notemanager', collection: 'user', repositoryClass: UserRepository::class)]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ODM\Id]
    private ?string $id;

    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Email has wrong format.')]
    #[Assert\Length(max: 150, maxMessage: 'Email must be at most {{limit}} characters long.')]
    #[ODM\Field(type: 'string')]
    private ?string $email;

    #[ODM\Field(type: 'collection')]
    private array $roles = [];

    #[ODM\Field(type: 'string')]
    private string $password = '';

    #[ODM\Field(type: 'date')]
    private ?DateTime $createdAt = null;

    #[ODM\ReferenceMany(targetDocument: Note::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {}

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

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUser($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            $note->setUser(null);
        }

        return $this;
    }
}
