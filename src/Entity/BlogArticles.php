<?php

namespace App\Entity;

use App\Repository\BlogArticlesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BlogArticlesRepository::class)
 */
class BlogArticles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $Description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $Username;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     * @Assert\Type("integer")
     */
    private $NumbersOfPages;

    /**
     * @ORM\ManyToOne(targetEntity=BlogCategories::class, inversedBy="article")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): self
    {
        $this->Username = $Username;

        return $this;
    }

    public function getNumbersOfPages(): ?int
    {
        return $this->NumbersOfPages;
    }

    public function setNumbersOfPages(int $NumbersOfPages): self
    {
        $this->NumbersOfPages = $NumbersOfPages;

        return $this;
    }

    public function getCategory(): ?BlogCategories
    {
        return $this->category;
    }

    public function setCategory(?BlogCategories $category): self
    {
        $this->category = $category;

        return $this;
    }
}
