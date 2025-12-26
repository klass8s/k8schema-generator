<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType;

final readonly class UnknownType extends AbstractType
{
    public static function fromJsonSchema(array $schema): UnknownType
    {
        return new self()->setSchema($schema);
    }
}
