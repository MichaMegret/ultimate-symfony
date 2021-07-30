<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom du produit est obligatoire")
     * @Assert\Length(min=3, max=255, minMessage="Le nom du produit doit comporter au moins 3 caractères", 
     * maxMessage="Le nom du produit doit comporter au maximum 255 caractères")
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=2)
     * @Assert\NotBlank(message="Le prix du produit est obligatoire", groups={"with-price"})
     * @Assert\GreaterThan(value=0, message="Le prix du produit doit être supérieur à 0", groups={"with-price"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'image est obligatoire")
     * @Assert\Url(message="L'url de l'image n'est pas valide")
     */
    private $mainPicture;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La description est obligatoire")
     * @Assert\Length(min=20, minMessage="La description doit faire au minimum 20 caractères")
     */
    private $shortDescription;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUppercaseName(): string{
        return strtoupper($this->name);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }


    //Méthode de validation d'une entité (pour ValidatorInetrface)
    //Peut être remplacer par les annotations @Assert

    // public static function loadValidatorMetadata(ClassMetadata $metadata): void
    // {
    //     $metadata->addPropertyConstraints("name", [
    //         new Assert\NotBlank(["message"=>"Le nom du produit est obligatoire"]),
    //         new Assert\Length([
    //             "min"=>3,
    //             "max"=>255,
    //             "minMessage"=>"Le nom du produit doit comporter au moins 3 caractères",
    //             "maxMessage"=>"Le nom du produit ne doit pas dépasser 255 caractères"
    //         ])
    //     ]);

    //     $metadata->addPropertyConstraint("price", new Assert\NotBlank(["message"=>"Le prix du produit doit être renseigné"]));
    // }

 
}
