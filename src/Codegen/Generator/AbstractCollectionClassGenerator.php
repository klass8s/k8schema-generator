<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\Parameter;

use function Symfony\Component\String\u;

abstract class AbstractCollectionClassGenerator
{
    protected const string NAMESPACE_PREFIX = 'Collection';
    protected const string PROPERTY_NAME = 'items';

    private GeneratedClassesCache $cache;

    abstract protected function addAllMethodBody(ClassType $class, PHPType $itemType, Parameter $param): string;
    abstract protected function clearMethodBody(): string;
    abstract protected function jsonSerializeMethodBody(): string;
    abstract protected function defineAddMethod(ClassType $class, PHPType $itemType): static;
    abstract protected static function classNameSuffix(): string;
    abstract protected static function propertyDocType(PHPType $itemType): string;
    abstract protected static function propertyType(): string;

    public function __construct(GeneratedClassesCache $cache)
    {
        $this->cache = $cache;
    }

    public function generate(PHPType $itemType, Context $context): PHPClass
    {
        $className = $this->className($itemType, $context);
        $fqcn = $className->fqcn();
        if ($this->cache->has($fqcn)) {
            return $this->cache->get($fqcn);
        }

        $class = new ClassType($className->shortName());
        $this
            ->generateForClass($class, $itemType)
            ->defineJsonSerializeMethod($class);

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\JsonSerializable::class));

        $this->cache->set($fqcn, $phpClass);

        return $phpClass;
    }

    public function generateForClass(ClassType $class, PHPType $itemType): static
    {
        $this
            ->defineProperty($class, $itemType)
            ->defineAddMethod($class, $itemType)
            ->defineAddAllMethod($class, $itemType)
            ->defineAllMethod($class, $itemType)
            ->defineClearMethod($class)
            ->defineCountMethod($class)
            ->defineConstructor($class)
            ->defineOtherMethods($class, $itemType)
        ;

        return $this;
    }

    protected function className(PHPType $type, Context $context): ClassName
    {
        $itemTypeName = $type->name;
        $namespacePrefix = static::NAMESPACE_PREFIX;
        if (ClassName::isFQCN($itemTypeName)) {
            $itemClassName = ClassName::fromFQCN($itemTypeName);
            $itemTypeName = $itemClassName->shortName();
            $namespacePrefix = $itemClassName->namespace();
            $namespacePrefix = u($namespacePrefix)->trimPrefix($context->namespacePrefix.'\\');
        }

        $namespaceName = $context->namespacePrefix.'\\'.$namespacePrefix;

        // If union type
        if (str_contains($itemTypeName, '|')) {
            $types = \explode('|', $itemTypeName);
            $types = \array_map(fn (string $type) => \ucfirst($type), $types);
            $shortClassName = implode('Or', $types);
        } else {
            $shortClassName = \ucfirst($itemTypeName);
        }

        $shortClassName .= static::classNameSuffix();

        return ClassName::fromNamespaceAndName($namespaceName, $shortClassName);
    }

    protected function defineAddAllMethod(ClassType $class, PHPType $itemType): static
    {
        $method = $class
            ->addMethod('addAll')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $param = $method
            ->addParameter('items')
            ->setType('array')
            ->setNullable(false);

        $method
            ->addBody($this->addAllMethodBody($class, $itemType, $param))
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected function defineAllMethod(ClassType $class, PHPType $itemType): static
    {
        $method = $class
            ->addMethod('all')
            ->setReturnType(static::propertyType())
            ->setReturnNullable(false);

        $method
            ->addComment(PHP_EOL)
            ->addComment('@return '.$this->propertyDocType($itemType))
            ->addComment(PHP_EOL);

        $method->addBody(sprintf('return $this->%s;', self::PROPERTY_NAME));

        return $this;
    }

    protected function defineClearMethod(ClassType $class): static
    {
        $method = $class
            ->addMethod('clear')
            ->setReturnType('self')
            ->setReturnNullable(false);

        $method
            ->addBody($this->clearMethodBody())
            ->addBody('')
            ->addBody('return $this;');

        return $this;
    }

    protected function defineOtherMethods(ClassType $classType, PHPType $itemType): static
    {
        return $this;
    }

    final protected function defineCountMethod(ClassType $class): static
    {
        $class
            ->addMethod('count')
            ->setReturnType(PHPType::INT)
            ->setReturnNullable(false)
            ->addBody(sprintf('return count($this->%s);', self::PROPERTY_NAME))
        ;

        return $this;
    }

    final protected function defineConstructor(ClassType $class): static
    {
        $class
            ->addMethod('__construct')
            ->addBody('$this->clear();');

        return $this;
    }

    protected function defineJsonSerializeMethod(ClassType $class): static
    {
        $nonNullableReturnType = ltrim(static::propertyType(), '?')
                |> (static fn (string $type): string => str_replace(['|null', 'null|'], ['', ''], $type));
        $class
            ->addMethod('jsonSerialize')
            ->setReturnNullable(false)
            ->setReturnType('?'.$nonNullableReturnType)
            ->addBody($this->jsonSerializeMethodBody())
        ;

        $class->addImplement(\JsonSerializable::class);

        return $this;
    }

    protected function defineProperty(ClassType $class, PHPType $itemType): static
    {
        $class
            ->addProperty(self::PROPERTY_NAME)
            ->setPrivate()
            ->setType(static::propertyType())
            ->setNullable(false)
            ->addComment(PHP_EOL)
            ->addComment(
                sprintf('@var %s', static::propertyDocType($itemType))
            )
            ->addComment(PHP_EOL);

        return $this;
    }
}
