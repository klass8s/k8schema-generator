<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\ValueObject;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

interface VOClassGeneratorInterface
{
    public function generate(ClassName $className, DataTypeInterface $type, Context $context, PHPTypesService $service): PHPClass;
    public function supports(ClassName $className, DataTypeInterface $type): bool;
}
