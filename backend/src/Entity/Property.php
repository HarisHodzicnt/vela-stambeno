<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PropertyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
#[ORM\Table(name: 'properties')]
#[ORM\Index(columns: ['status', 'listing_type', 'city'], name: 'idx_property_search')]
#[ORM\Index(columns: ['owner_id'], name: 'idx_property_owner')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: "is_granted('ROLE_USER')",
            processor: \App\State\PropertyOwnerProcessor::class
        ),
        new Patch(security: "is_granted('ROLE_USER') and object.getOwner() == user"),
        new Delete(security: "is_granted('ROLE_USER') and object.getOwner() == user")
    ],
    normalizationContext: ['groups' => [self::GROUP_READ, 'user:read']],
    denormalizationContext: ['groups' => [self::GROUP_WRITE]]
)]
class Property
{
    public const GROUP_READ = 'property:read';
    public const GROUP_WRITE = 'property:write';
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([self::GROUP_READ])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'properties')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([self::GROUP_READ])]
    private ?User $owner = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['apartment', 'house', 'studio', 'villa', 'condo', 'land', 'commercial'])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $propertyType = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['rent', 'sale', 'both'])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $listingType = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $addressLine1 = null;


    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $municipality = null;


    #[ORM\Column(length: 2, options: ['default' => 'BA'])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private string $country = 'BA';



    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?int $bedrooms = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $bathrooms = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $sizeSqm = null;




    // Rental pricing
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $pricePerNight = null;






    // Sale pricing
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $salePrice = null;




    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private bool $instantBook = false;

    #[ORM\Column(length: 20, options: ['default' => 'draft'])]
    #[Groups([self::GROUP_READ])]
    private string $status = 'draft';


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([self::GROUP_READ, self::GROUP_WRITE])]
    private ?string $coverImageUrl = null;





    #[ORM\Column(length: 255, unique: true)]
    #[Groups([self::GROUP_READ])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups([self::GROUP_READ])]
    private ?\DateTimeInterface $soldAt = null;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: PropertyImage::class, cascade: ['persist', 'remove'])]
    #[Groups([self::GROUP_READ])]
    private Collection $images;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: RentalBooking::class)]
    private Collection $rentalBookings;

    #[ORM\OneToMany(mappedBy: 'property', targetEntity: PurchaseOffer::class)]
    private Collection $purchaseOffers;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'savedProperties')]
    private Collection $savedByUsers;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->rentalBookings = new ArrayCollection();
        $this->purchaseOffers = new ArrayCollection();
        $this->savedByUsers = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function getPropertyType(): ?string
    {
        return $this->propertyType;
    }

    public function setPropertyType(string $propertyType): static
    {
        $this->propertyType = $propertyType;
        return $this;
    }

    public function getListingType(): ?string
    {
        return $this->listingType;
    }

    public function setListingType(string $listingType): static
    {
        $this->listingType = $listingType;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        $this->generateSlug();
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function setAddressLine1(string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    public function setMunicipality(?string $municipality): static
    {
        $this->municipality = $municipality;
        return $this;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function setBathrooms(string $bathrooms): static
    {
        $this->bathrooms = $bathrooms;
        return $this;
    }

    public function setSizeSqm(string $sizeSqm): static
    {
        $this->sizeSqm = $sizeSqm;
        return $this;
    }

    public function setCoverImageUrl(?string $coverImageUrl): static
    {
        $this->coverImageUrl = $coverImageUrl;
        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;
        return $this;
    }

    public function getPricePerNight(): ?string
    {
        return $this->pricePerNight;
    }

    public function setPricePerNight(?string $pricePerNight): static
    {
        $this->pricePerNight = $pricePerNight;
        return $this;
    }

    public function getSalePrice(): ?string
    {
        return $this->salePrice;
    }

    public function setSalePrice(?string $salePrice): static
    {
        $this->salePrice = $salePrice;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        
        if ($status === 'active' && $this->publishedAt === null) {
            $this->publishedAt = new \DateTime();
        }
        
        if ($status === 'sold') {
            $this->soldAt = new \DateTime();
        }
        
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    private function generateSlug(): void
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->title)));
        $this->slug = $slug . '-' . substr(uniqid(), -6);
    }

    public function isInstantBook(): bool
    {
        return $this->instantBook;
    }

    public function setInstantBook(bool $instantBook): static
    {
        $this->instantBook = $instantBook;
        return $this;
    }

    /**
     * @return Collection<int, PropertyImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PropertyImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProperty($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, RentalBooking>
     */
    public function getRentalBookings(): Collection
    {
        return $this->rentalBookings;
    }

    /**
     * @return Collection<int, PurchaseOffer>
     */
    public function getPurchaseOffers(): Collection
    {
        return $this->purchaseOffers;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
