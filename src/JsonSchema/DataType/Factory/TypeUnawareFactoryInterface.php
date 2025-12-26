<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

interface TypeUnawareFactoryInterface extends DataTypeFactoryInterface
{
    /**
     * This method tells whether factory can create corresponding data type using given schema
     *
     * @param array<string, mixed> $schema
     */
    public function recognizesSchema(array $schema): bool;
}
