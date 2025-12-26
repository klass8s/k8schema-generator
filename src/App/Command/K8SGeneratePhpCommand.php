<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Tequila\K8S\SchemaGenerator\Codegen\K8SCodeGenerationService;

#[AsCommand(
    name: 'k8s:generate:php',
    description: 'Generates PHP classes from Kubernetes json schema',
    aliases: ['k8s:gen:php']
)]
class K8SGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    public function __construct(K8SCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
