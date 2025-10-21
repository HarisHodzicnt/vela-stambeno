<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Property;
use Symfony\Bundle\SecurityBundle\Security;

final class PropertyOwnerProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Property && $operation instanceof \ApiPlatform\Metadata\Post) {
            // Automatically set the owner to the currently authenticated user
            $user = $this->security->getUser();
            if ($user) {
                $data->setOwner($user);
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
