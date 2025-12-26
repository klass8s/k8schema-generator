<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType;

final readonly class ReferenceType extends AbstractType
{
    public function __construct(private string $referencedDefinitionName)
    {
    }

    public function referencedDefinitionName(): string
    {
        return $this->referencedDefinitionName;
    }
}
