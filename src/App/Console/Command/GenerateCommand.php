<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Tequila\K8S\SchemaGenerator\Codegen\K8SCodeGenerationService;

#[AsCommand(
    name: 'generate',
    description: 'Generates PHP classes from Kubernetes json schema',
)]
class GenerateCommand extends AbstractCodeGenerationCommand
{
    public function __construct(K8SCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
