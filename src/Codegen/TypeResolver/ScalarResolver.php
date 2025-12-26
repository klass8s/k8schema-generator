<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\BoolType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\IntegerType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\NumberType;

class ScalarResolver extends AbstractTypeResolver
{
    private const array TYPES_MAP = [
        BoolType::class => PHPType::BOOL,
        IntegerType::class => PHPType::INT,
        NumberType::class => PHPType::FLOAT,
    ];

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $phpType = $this->phpType($type);
        if ($nullable) {
            $phpType .= '|null';
        }

        return new PHPType($phpType, $phpType, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return array_key_exists($type::class, self::TYPES_MAP);
    }

    protected function phpType(DataTypeInterface $type): string
    {
        return self::TYPES_MAP[$type::class];
    }
}
