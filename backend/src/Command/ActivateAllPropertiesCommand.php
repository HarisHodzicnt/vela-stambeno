<?php

namespace App\Command;

use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:activate-properties',
    description: 'Sets the status of all draft properties to active.',
)]
class ActivateAllPropertiesCommand extends Command
{
    private $propertyRepository;
    private $entityManager;

    public function __construct(PropertyRepository $propertyRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->propertyRepository = $propertyRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $draftProperties = $this->propertyRepository->findBy(['status' => 'draft']);

        if (empty($draftProperties)) {
            $io->note('No draft properties found to activate.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($draftProperties as $property) {
            $property->setStatus('active');
            $count++;
        }

        $this->entityManager->flush();

        $io->success(sprintf('Successfully activated %d properties.', $count));

        return Command::SUCCESS;
    }
}
