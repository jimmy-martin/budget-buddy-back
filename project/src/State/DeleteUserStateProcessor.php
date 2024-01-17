<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DeleteUserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (false === $data instanceof User) {
            return;
        }

        if (!$data->canBeDeleted()) {
            return;
        }

        $data->setIsDeleted(true);

        $this->entityManager->flush();
    }
}