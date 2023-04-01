<?php

namespace App\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Ignore;
use App\Repository\AdditionalParameterRepository;
use App\Entity\EntityInterface;
use App\Entity\Note;

#[ODM\Document(db: 'notemanager', collection: 'additional_parameter', repositoryClass: AdditionalParameterRepository::class)]
class AdditionalParameter implements EntityInterface
{
    #[ODM\Id]
    private ?string $id;

    #[Assert\NotBlank(message: 'Parameter name is required.')]
    #[Assert\Length(max: 255, maxMessage: 'Parameter name must be at most {{limit}} characters long.')]
    #[ODM\Field(type: 'string')]
    private string $name;

    #[Assert\NotBlank(message: 'Value is required.')]
    #[Assert\Length(max: 4095, maxMessage: 'Value must be at most {{limit}} characters long.')]
    #[ODM\Field(type: 'string')]
    private string $value;

    #[Ignore]
    #[ODM\ReferenceOne(targetDocument: Note::class, inversedBy: 'additionalParameters')]
    private ?Note $note = null;

    public function getId(): ?string
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function setNote(?Note $note): self
    {
        $this->note = $note;

        return $this;
    }
}
