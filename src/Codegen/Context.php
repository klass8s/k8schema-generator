<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen;

use Klass8s\K8SchemaGenerator\Codegen\Definition\ApiObjectDefinitionsMap;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\JsonSchema\TypesMap;

final readonly class Context
{
    public function __construct(
        public ApiObjectDefinitionsMap $definitions,
        public TypesMap $types,
        public string $outputDir,
        public string $namespacePrefix,
        public ClassName $resourceInterface,
        public ClassName $resourceListInterface,
    ) {
    }
}
