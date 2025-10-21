<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PurchaseOfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseOfferRepository::class)]
#[ORM\Table(name: 'purchase_offers')]
#[ApiResource(
    normalizationContext: ['groups' => ['offer:read']],
    denormalizationContext: ['groups' => ['offer:write']]
)]
class PurchaseOffer
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['offer:read'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseOffers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['offer:read'])]
    private ?Property $property = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseOffers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['offer:read'])]
    private ?User $buyer = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['offer:read', 'offer:write'])]
    private ?string $offerAmount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offer:read', 'offer:write'])]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['cash', 'mortgage', 'mixed'])]
    #[Groups(['offer:read', 'offer:write'])]
    private ?string $financingType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offer:read', 'offer:write'])]
    private ?string $contingencies = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['offer:read', 'offer:write'])]
    private ?\DateTimeInterface $proposedClosingDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['offer:read', 'offer:write'])]
    private ?\DateTimeInterface $offerValidUntil = null;

    #[ORM\Column(length: 30, options: ['default' => 'pending'])]
    #[Groups(['offer:read'])]
    private string $status = 'pending';

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    #[Groups(['offer:read'])]
    private ?string $counterOfferAmount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offer:read'])]
    private ?string $counterOfferMessage = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['offer:read'])]
    private ?\DateTimeInterface $counterOfferAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['offer:read'])]
    private ?\DateTimeInterface $respondedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offer:read'])]
    private ?string $responseMessage = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['offer:read'])]
    private bool $contractSigned = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['offer:read'])]
    private ?\DateTimeInterface $contractSignedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['offer:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;
        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getOfferAmount(): ?string
    {
        return $this->offerAmount;
    }

    public function setOfferAmount(string $offerAmount): static
    {
        $this->offerAmount = $offerAmount;
        return $this;
    }

    public function getFinancingType(): ?string
    {
        return $this->financingType;
    }

    public function setFinancingType(string $financingType): static
    {
        $this->financingType = $financingType;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        
        if (in_array($status, ['accepted', 'rejected', 'countered']) && $this->respondedAt === null) {
            $this->respondedAt = new \DateTime();
        }
        
        return $this;
    }

    public function accept(?string $responseMessage = null): void
    {
        $this->status = 'accepted';
        $this->responseMessage = $responseMessage;
        $this->respondedAt = new \DateTime();
        
        // Update property status
        if ($this->property) {
            $this->property->setStatus('under_contract');
        }
    }

    public function reject(?string $responseMessage = null): void
    {
        $this->status = 'rejected';
        $this->responseMessage = $responseMessage;
        $this->respondedAt = new \DateTime();
    }

    public function counter(string $counterAmount, ?string $counterMessage = null): void
    {
        $this->status = 'countered';
        $this->counterOfferAmount = $counterAmount;
        $this->counterOfferMessage = $counterMessage;
        $this->counterOfferAt = new \DateTime();
        $this->respondedAt = new \DateTime();
    }

    public function withdraw(): void
    {
        $this->status = 'withdrawn';
        $this->respondedAt = new \DateTime();
    }

    public function isExpired(): bool
    {
        return $this->offerValidUntil < new \DateTime() && $this->status === 'pending';
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
