<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType\Factory;

interface TypeAwareFactoryInterface extends DataTypeFactoryInterface
{
    /**
     * This method tells which values of "type" field in JSON schema it expects.
     * It's used to speed up corresponding factory determination for any given typed schema
     *
     * @return string[]
     */
    public function expectedTypes(): array;
}
