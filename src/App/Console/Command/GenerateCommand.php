<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Klass8s\K8SchemaGenerator\Codegen\K8SCodeGenerationService;

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
