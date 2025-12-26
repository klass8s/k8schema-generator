<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\AbstractUnionType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

class UnionTypeResolver extends AbstractTypeResolver
{
    public function resolve(DataTypeInterface|AbstractUnionType $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $types = $type->types();
        $union = [];
        foreach ($types as $underlyingType) {
            $phpType = $service->resolveType($underlyingType, $context, false);
            $union[] = $phpType->name;
        }
        $union = array_unique($union);
        if (!in_array('null', $union) && $nullable) {
            $union[] = 'null';
        }
        $unionType = implode('|', $union);

        return new PHPType($unionType, $unionType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof AbstractUnionType;
    }
}
