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
                    $valueParam->getName()
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected function defineAddAllMethodBody(ClassType $class, PHPType $itemType, Parameter $param): string
    {
        return sprintf(
            <<<'BODY'
                foreach ($%s as $value) {
                    $this->add($value);
                }
                BODY,
            $param->getName()
        );
    }

    protected static function classNameSuffix(): string
    {
        return self::CLASS_NAME_SUFFIX;
    }
}
