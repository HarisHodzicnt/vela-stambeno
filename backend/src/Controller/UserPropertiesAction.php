<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UserPropertiesAction
{
    public function __construct(private readonly PropertyRepository $propertyRepository)
    {
    }

    public function __invoke(User $user): array
    {
        return $this->propertyRepository->findBy(['owner' => $user]);
    }
}
