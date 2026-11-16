<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\ValueObject;

use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\ClassType;

abstract class AbstractGenerator implements VOClassGeneratorInterface
{
    protected const string VALUE_PROPERTY_NAME = 'value';

    protected function defineProperty(ClassType $class, PHPType $phpType): self
    {
        $property = $class->addProperty(self::VALUE_PROPERTY_NAME);
        $property
            ->setPrivate()
            ->setType($phpType->name)
            ->setNullable(false)
            ->addComment(PHP_EOL)
            ->addComment('@var '.$phpType->docType)
            ->addComment(PHP_EOL);

        return $this;
    }

    protected function defineConstructor(ClassType $class, PHPType $phpType): self
    {
        $constructor = $class->addMethod('__construct');
        $constructor->setPrivate();
        $param = $constructor->addParameter(self::VALUE_PROPERTY_NAME);
        $param
            ->setType($phpType->name)
            ->setNullable(false);
        $constructor->addComment(
            sprintf('@param %s $%s', $phpType->docType, $param->getName())
        );
        $constructor->addBody(
            sprintf('$this->%s = $%s;', $param->getName(), $param->getName())
        );

        return $this;
    }

    protected function defineFactoryMethod(ClassType $class, PHPType $phpType, ?string $methodName = null, ?string $paramName = null): self
    {
        $methodName ??= 'from'.ucfirst($phpType->name);
        $factoryMethod = $class->addMethod($methodName);
        $factoryMethod
            ->setPublic()
            ->setStatic()
            ->setReturnType($class->getName())
            ->setReturnNullable(false);
        $paramName ??= \lcfirst($phpType->name);
        $param = $factoryMethod->addParameter($paramName);
        $param
            ->setType($phpType->name)
            ->setNullable(false);
        $factoryMethod->addBody(
            sprintf('return new self($%s);', $param->getName())
        );

        return $this;
    }

    protected function defineJsonSerializeMethod(PHPClass $class): self
    {
        $method = $class->classType()->addMethod('jsonSerialize')->setReturnType('mixed');
        $method->addBody(
            $this->jsonSerializeBody()
        );
        $class->classType()->addImplement(\JsonSerializable::class);
        $class->useClass(ClassName::fromFQCN(\JsonSerializable::class));

        return $this;
    }

    protected function jsonSerializeBody(): string
    {
        return sprintf('return $this->%s;', self::VALUE_PROPERTY_NAME);
    }
}
