<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

interface TypeResolverInterface
{
    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType;
    public function supports(DataTypeInterface $type): bool;
}
