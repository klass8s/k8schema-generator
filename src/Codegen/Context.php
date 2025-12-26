<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen;

use Tequila\K8S\SchemaGenerator\Codegen\Definition\ApiObjectDefinitionsMap;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\TypesMap;

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
