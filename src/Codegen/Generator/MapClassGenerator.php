<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;

final class MapClassGenerator extends AbstractCollectionClassGenerator
{
    private const string CLASS_NAME_SUFFIX = 'Map';

    protected function defineAddMethod(ClassType $class, PHPType $itemType): static
    {
        $method = $class
            ->addMethod('add')
            ->setReturnType('self')
            ->setReturnNullable(false);
        $nameParam = $this->defineNameParam($method);
        $valueParam = $method
            ->addParameter('value')
            ->setType($itemType->name)
            ->setNullable(false);

        $method
            ->addBody(
                sprintf(
                    '$this->%s[$%s] = $%s;',
                    self::PROPERTY_NAME,
                    $nameParam->getName(),
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
                    foreach ($%s as $key => $value) {
                        $this->add($key, $value);
                    }
                BODY,
            $param->getName(),
        );
    }

    protected function clearMethodBody(): string
    {
        return sprintf('$this->%s = new \ArrayObject();', self::PROPERTY_NAME);
    }

    protected function defineOtherMethods(ClassType $classType, PHPType $itemType): static
    {
        $this
            ->defineHasMethod($classType)
            ->defineGetMethod($classType, $itemType)
            ->defineRemoveMethod($classType);

        return $this;
    }

    private function defineHasMethod(ClassType $class): self
    {
        $method = $class
            ->addMethod('has')
            ->setReturnType('bool')
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);
        $method->addBody(
            sprintf(
                'return $this->%s->offsetExists($%s);',
                self::PROPERTY_NAME,
                $param->getName(),
            )
        );

        return $this;
    }

    private function defineGetMethod(ClassType $class, PHPType $itemType): self
    {
        $method = $class
            ->addMethod('get')
            ->setReturnType($itemType->name)
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);

        $method->addBody(
            sprintf(
                'return $this->%s[$%s];',
                self::PROPERTY_NAME,
                $param->getName(),
            )
        );

        return $this;
    }

    private function defineRemoveMethod(ClassType $class): void
    {
        $method = $class
            ->addMethod('remove')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $param = $this->defineNameParam($method);
        $method
            ->addBody(
                sprintf('$this->%s->offsetUnset($%s);', self::PROPERTY_NAME, $param->getName())
            )
            ->addBody('')
            ->addBody('return $this;');

    }

    private function defineNameParam(Method $method): Parameter
    {
        return $method
            ->addParameter('name')
            ->setType('string')
            ->setNullable(false);
    }

    protected static function classNameSuffix(): string
    {
        return self::CLASS_NAME_SUFFIX;
    }

    protected static function propertyDocType(PHPType $itemType): string
    {
        $types = \explode('|', $itemType->name);
        $types = array_map(static fn (string $type) => sprintf('\ArrayObject<string, %s>', $type), $types);

        return implode('|', $types);
    }

    protected static function propertyType(): string
    {
        return '\ArrayObject';
    }
}
