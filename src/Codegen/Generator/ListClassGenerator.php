<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\Parameter;

final class ListClassGenerator extends AbstractCollectionClassGenerator
{
    private const string CLASS_NAME_SUFFIX = 'List';

    protected function defineAddMethod(ClassType $class, PHPType $itemType): static
    {
        $method = $class
            ->addMethod('add')
            ->setReturnType('self')
            ->setReturnNullable(false);
        $valueParam = $method
            ->addParameter('value')
            ->setType($itemType->name)
            ->setNullable(false);

        $method
            ->addBody(
                sprintf(
                    '$this->%s[] = $%s;',
                    self::PROPERTY_NAME,
                    $valueParam->getName(),
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected function addAllMethodBody(ClassType $class, PHPType $itemType, Parameter $param): string
    {
        return sprintf(
            <<<'BODY'
                    foreach ($%s as $value) {
                        $this->add($value);
                    }
                BODY,
            $param->getName(),
        );
    }

    protected function clearMethodBody(): string
    {
        return sprintf('$this->%s = [];', self::PROPERTY_NAME);
    }

    protected function jsonSerializeMethodBody(): string
    {
        return sprintf('return count($this->%s) > 0 ? $this->%s : null;', self::PROPERTY_NAME, self::PROPERTY_NAME);
    }

    protected static function classNameSuffix(): string
    {
        return self::CLASS_NAME_SUFFIX;
    }

    protected static function propertyDocType(PHPType $itemType): string
    {
        $types = \explode('|', $itemType->name);
        $types = array_map(static fn (string $type) => $type.'[]', $types);

        return implode('|', $types);
    }

    protected static function propertyType(): string
    {
        return 'array';
    }
}
