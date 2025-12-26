<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Type;

use Symfony\Component\String\AbstractUnicodeString;
use Tequila\K8S\SchemaGenerator\App\Util\Str;

use function Symfony\Component\String\u;

final readonly class ClassName
{
    private string $shortName;
    private string $fqcn;

    private function __construct(private string $namespace, string $shortName)
    {
        $this->shortName = self::validClassName($shortName);
        $this->fqcn = $this->namespace.'\\'.$this->shortName;
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function shortName(): string
    {
        return $this->shortName;
    }

    public function fqcn(): string
    {
        return $this->fqcn;
    }

    public static function isFQCN(string $string): bool
    {
        return str_contains($string, '\\') && !\str_contains($string, '|');
    }

    public static function fromNamespaceAndName(string $namespace, string $shortName): self
    {
        return new self($namespace, $shortName);
    }

    public static function fromFQCN(string $fqcn): self
    {
        $parts = \explode('\\', $fqcn);
        $shortName = \array_pop($parts);
        $namespace = \implode('\\', $parts);
        $namespace = trim($namespace, '\\');

        return new self($namespace, $shortName);
    }

    public static function fromDefinitionName(string $namespace, string $definitionName): self
    {
        $definitionName = u($definitionName)->trimPrefix('io.k8s.');
        $parts = $definitionName->split('.');
        $parts = array_map(
            fn (AbstractUnicodeString $part) => self::validClassName(Str::asClassName($part->toString())),
            $parts,
        );
        $shortName = array_pop($parts);
        $namespace = $namespace.'\\'.implode('\\', $parts);

        return new self($namespace, $shortName);
    }

    private static function validClassName(string $className): string
    {
        return match ($className) {
            'Function' => 'AFunction',
            'Object' => 'AnObject',
            'Namespace' => 'KubernetesNamespace',
            default => $className,
        };
    }
}
