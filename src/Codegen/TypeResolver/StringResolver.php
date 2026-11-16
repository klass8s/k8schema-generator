<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\TypeResolver;

use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\StringType;

class StringResolver extends ScalarResolver
{
    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME !== $type->format();
    }

    protected function phpType(DataTypeInterface $type): string
    {
        return PHPType::STRING;
    }
}
