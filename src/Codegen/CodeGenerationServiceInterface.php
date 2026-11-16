<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen;

use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;

interface CodeGenerationServiceInterface
{
    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir, ?ClassName $resourceInterface = null, ?ClassName $resourceListInterface = null): void;
}
