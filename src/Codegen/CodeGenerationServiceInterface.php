<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen;

use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;

interface CodeGenerationServiceInterface
{
    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir, ?ClassName $resourceInterface = null, ?ClassName $resourceListInterface = null): void;
}
