<?php

namespace App\Entity;

use DateTime;
use App\Entity\PurchaseItem;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass=PurchaseRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Purchase
{

    public const STATUT_PENDING = "PENDING";
    public const STATUT_PAID = "PAID";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=2)
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut = "PENDING";

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="purchases")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $purchasedAt;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="purchase", orphanRemoval=true)
     * @var Collection<PurchaseItem>
     */
    private $purchaseItems;

    /**
     * @ORM\OneToOne(targetEntity=PurchaseSuspicion::class, mappedBy="purchase", cascade={"persist", "remove"})
     */
    private $suspicion;


    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
    }

    // Utilisation des évenements Doctrine
    //------------------------------------------------------------------------------------------------------------

    /**
     * @ORM\PrePersist
     */
    public function prePersist(){

        if(empty($this->purchasedAt)){
            $this->purchasedAt = new DateTime;
        }
    }


    /**
     * @ORM\PreFlush
     */
    public function preFlush(){

        $total = 0;
        
        foreach($this->getPurchaseItems() as $item){
            /** @var PurchaseItem */
            $item=$item;
            $total+=$item->getTotal();
        }
        $this->total = $total;
    }


    
    //------------------------------------------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
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

    public function getPurchasedAt(): ?\DateTime
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTime $purchasedAt): self
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setPurchase($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getPurchase() === $this) {
                $purchaseItem->setPurchase(null);
            }
        }

        return $this;
    }

    public function getSuspicion(): ?PurchaseSuspicion
    {
        return $this->suspicion;
    }

    public function setSuspicion(PurchaseSuspicion $suspicion): self
    {
        // set the owning side of the relation if necessary
        if ($suspicion->getPurchase() !== $this) {
            $suspicion->setPurchase($this);
        }

        $this->suspicion = $suspicion;

        return $this;
    }


}
