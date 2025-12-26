<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\StringType;

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
