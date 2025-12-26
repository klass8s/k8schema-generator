<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Type;

final readonly class PHPType
{
    public const string BOOL = 'bool';
    public const string INT = 'int';
    public const string FLOAT = 'float';
    public const string STRING = 'string';

    public function __construct(
        public ?string $name,
        public string $docType,
        public bool $nullable,
    ) {
    }
}
