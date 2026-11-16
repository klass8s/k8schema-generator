<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\TypeResolver;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\BoolType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\IntegerType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\NumberType;

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
