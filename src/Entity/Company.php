<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @Groups("company")
     * @Groups("master")
     * @Groups("creditcard")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("company")
     * @Groups("master")
     * @Groups("creditcard")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Groups("company")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $slogan;

    /**
     * @Groups("company")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @Groups("company")
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @Groups("company")
     * @Assert\Url()
     * @ORM\Column(type="text", nullable=true)
     */
    private $websiteUrl;

    /**
     * @Groups("company")
     * @Assert\Url()
     * @ORM\Column(type="text", nullable=true)
     */
    private $pictureUrl;

    /**
     * @Groups("company")
     * @ORM\OneToOne(targetEntity="App\Entity\Master", inversedBy="company", cascade={"persist", "remove"})
     */
    private $master;

    /**
     * @Groups("company")
     * @ORM\OneToMany(targetEntity="App\Entity\Creditcard", mappedBy="company", orphanRemoval=true)
     */
    private $creditcards;

    public function __construct()
    {
        $this->creditcards = new ArrayCollection();
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

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(string $slogan): self
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): self
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(?string $pictureUrl): self
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    public function getMaster(): ?Master
    {
        return $this->master;
    }

    public function setMaster(?Master $master): self
    {
        $this->master = $master;

        return $this;
    }

    /**
     * @return Collection|Creditcard[]
     */
    public function getCreditcards(): Collection
    {
        return $this->creditcards;
    }

    public function addCreditcard(Creditcard $creditcard): self
    {
        if (!$this->creditcards->contains($creditcard)) {
            $this->creditcards[] = $creditcard;
            $creditcard->setCompany($this);
        }

        return $this;
    }

    public function removeCreditcard(Creditcard $creditcard): self
    {
        if ($this->creditcards->contains($creditcard)) {
            $this->creditcards->removeElement($creditcard);
            // set the owning side to null (unless already changed)
            if ($creditcard->getCompany() === $this) {
                $creditcard->setCompany(null);
            }
        }

        return $this;
    }
}
