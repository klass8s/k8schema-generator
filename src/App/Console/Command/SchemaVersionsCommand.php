<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequila\K8S\SchemaGenerator\App\Console\UI\ModernStyle;
use Tequila\K8S\SchemaGenerator\App\Kubernetes\JsonSchemaVersionsService;

#[AsCommand(
    name: 'schema:versions',
    description: 'This command retrieves the latest json schema versions for a number of Kubernetes minor versions.'
)]
class SchemaVersionsCommand extends Command
{
    private const string ARGUMENT_NAME = 'numberOfVersions';
    private const int DEFAULT_NUMBER_OF_VERSIONS = 4;

    public function __construct(private readonly JsonSchemaVersionsService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_NAME,
                InputArgument::OPTIONAL,
                'The number of latest Kubernetes versions to retrieve json schema versions for',
                self::DEFAULT_NUMBER_OF_VERSIONS
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);
        $numberOfVersions = $input->getArgument(self::ARGUMENT_NAME);

        $map = $this->service->latestVersionsMap($numberOfVersions);

        $io->writeln(json_encode($map));

        return self::SUCCESS;
    }
}
