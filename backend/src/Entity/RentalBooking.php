<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RentalBookingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RentalBookingRepository::class)]
#[ORM\Table(name: 'rental_bookings')]
#[ApiResource(
    normalizationContext: ['groups' => ['booking:read']],
    denormalizationContext: ['groups' => ['booking:write']]
)]
class RentalBooking
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['booking:read'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'rentalBookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['booking:read'])]
    private ?Property $property = null;

    #[ORM\ManyToOne(inversedBy: 'rentalBookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['booking:read'])]
    private ?User $guest = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $checkInDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?\DateTimeInterface $checkOutDate = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?int $totalNights = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Positive]
    #[Groups(['booking:read', 'booking:write'])]
    private ?int $numberOfGuests = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $guestPhone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['booking:read', 'booking:write'])]
    private ?string $specialRequests = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['booking:read'])]
    private ?string $basePrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private string $cleaningFee = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['booking:read'])]
    private ?string $serviceFee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['booking:read'])]
    private ?string $totalPrice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Groups(['booking:read'])]
    private string $depositAmount = '0.00';

    #[ORM\Column(length: 30, options: ['default' => 'pending'])]
    #[Groups(['booking:read'])]
    private string $paymentStatus = 'pending';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paymentIntentId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['booking:read'])]
    private bool $depositHeld = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $depositReleasedAt = null;

    #[ORM\Column(length: 30, options: ['default' => 'pending'])]
    #[Groups(['booking:read'])]
    private string $status = 'pending';

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['booking:read'])]
    private bool $requiresOwnerApproval = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['booking:read'])]
    private ?\DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $cancelledAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancellationReason = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?string $cancelledBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checkedInAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $checkedOutAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['booking:read'])]
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

    public function getGuest(): ?User
    {
        return $this->guest;
    }

    public function setGuest(?User $guest): static
    {
        $this->guest = $guest;
        return $this;
    }

    public function getCheckInDate(): ?\DateTimeInterface
    {
        return $this->checkInDate;
    }

    public function setCheckInDate(\DateTimeInterface $checkInDate): static
    {
        $this->checkInDate = $checkInDate;
        $this->calculateTotalNights();
        return $this;
    }

    public function getCheckOutDate(): ?\DateTimeInterface
    {
        return $this->checkOutDate;
    }

    public function setCheckOutDate(\DateTimeInterface $checkOutDate): static
    {
        $this->checkOutDate = $checkOutDate;
        $this->calculateTotalNights();
        return $this;
    }

    private function calculateTotalNights(): void
    {
        if ($this->checkInDate && $this->checkOutDate) {
            $diff = $this->checkInDate->diff($this->checkOutDate);
            $this->totalNights = $diff->days;
        }
    }

    public function getTotalNights(): ?int
    {
        return $this->totalNights;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        
        if ($status === 'confirmed' && $this->confirmedAt === null) {
            $this->confirmedAt = new \DateTime();
        }
        
        if ($status === 'cancelled' && $this->cancelledAt === null) {
            $this->cancelledAt = new \DateTime();
        }
        
        return $this;
    }

    public function calculatePricing(float $platformFeePercentage = 0.10): void
    {
        if ($this->property && $this->totalNights) {
            $pricePerNight = (float) $this->property->getPricePerNight();
            $this->basePrice = (string) ($pricePerNight * $this->totalNights);
            $this->cleaningFee = $this->property->getCleaningFee();
            $this->serviceFee = (string) ((float) $this->basePrice * $platformFeePercentage);
            $this->totalPrice = (string) ((float) $this->basePrice + (float) $this->cleaningFee + (float) $this->serviceFee);
            $this->depositAmount = $this->property->getDepositAmount();
        }
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
