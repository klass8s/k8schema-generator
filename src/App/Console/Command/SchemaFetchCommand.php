<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tequila\K8S\SchemaGenerator\App\Console\UI\ModernStyle;

#[AsCommand(
    name: 'schema:fetch',
    description: 'Fetches Kubernetes json schema and saves to file',
)]
class SchemaFetchCommand extends Command
{
    private const string K8S_VERSION_PATTERN = '/^v(\d+)\.(\d+)$/';
    private const string ARGUMENT_K8S_VERSION = 'k8sVersion';
    private const string ARGUMENT_FILE_PATH = 'filePath';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly array $jsonSchemaVersions,
        private readonly string $jsonSchemaUrlTemplate,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_K8S_VERSION,
                InputArgument::REQUIRED,
                'A major and minor version of Kubernetes API, e.g. "v1.35"',
            )
            ->addArgument(
                self::ARGUMENT_FILE_PATH,
                InputArgument::REQUIRED,
                'Path where to save fetched json schema',
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $k8sVersion = trim($input->getArgument(self::ARGUMENT_K8S_VERSION));
        if (empty($k8sVersion)) {
            $io->error('Kubernetes version cannot be empty');
        }

        if (!preg_match(self::K8S_VERSION_PATTERN, $k8sVersion)) {
            $io->error(sprintf('Kubernetes version "%s" is invalid', $k8sVersion));
        }

        if (!array_key_exists($k8sVersion, $this->jsonSchemaVersions)) {
            $io->error(sprintf('Kubernetes version "%s" is not supported. Supported versions are: "%s".', $k8sVersion, implode('", "', array_keys($this->jsonSchemaVersions))));
        }

        $schemaVersion = $this->jsonSchemaVersions[$k8sVersion];
        $jsonSchemaUrl = str_replace('{version}', $schemaVersion, $this->jsonSchemaUrlTemplate);

        $filePath = $input->getArgument(self::ARGUMENT_FILE_PATH);
        $json = $this->fetchJson($jsonSchemaUrl);
        file_put_contents($filePath, $json);

        $io->success(sprintf('Json schema saved to file "%s"', $filePath));
        $io->writeln('');

        return self::SUCCESS;
    }

    private function fetchJson(string $jsonSchemaUrl): string
    {
        try {
            $response = $this->httpClient->request('GET', $jsonSchemaUrl);

            return $response->getContent(true);
        } catch (ExceptionInterface $e) {
            throw new \RuntimeException(
                sprintf(
                    "Cannot fetch json schema from URL '%s':\n%s with message:\n%s",
                    $jsonSchemaUrl,
                    $e::class,
                    $e->getMessage()
                )
            );
        }
    }
}
