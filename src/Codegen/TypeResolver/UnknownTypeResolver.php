<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\TypeResolver;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\UnknownType;

class UnknownTypeResolver extends AbstractTypeResolver
{
    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        return new PHPType('mixed', 'mixed', false);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof UnknownType;
    }
}
