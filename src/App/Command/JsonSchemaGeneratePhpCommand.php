<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Tequila\K8S\SchemaGenerator\Codegen\GenericCodeGenerationService;

#[AsCommand(
    name: 'json-schema:generate:php',
    description: 'Generates PHP classes from json schema',
    aliases: ['json:gen:php']
)]
class JsonSchemaGeneratePhpCommand extends AbstractCodeGenerationCommand
{
    public function __construct(GenericCodeGenerationService $service)
    {
        parent::__construct($service);
    }
}
