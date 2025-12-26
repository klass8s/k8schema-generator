<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\JsonSchema\DataType;

interface DataTypeInterface
{
    public const string TYPE_ARRAY = 'array';
    public const string TYPE_BOOL = 'boolean';
    public const string TYPE_INTEGER = 'integer';
    public const string TYPE_NUMBER = 'number';
    public const string TYPE_OBJECT = 'object';
    public const string TYPE_STRING = 'string';
    public const string TYPE_REFERENCE = 'reference';

    public function description(): string;

    public function setSchema(array $schema): DataTypeInterface;

    public function schema(): array;

    public function hasAnnotation(string $name): bool;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getAnnotation(string $name): mixed;
}
