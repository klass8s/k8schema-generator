<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Symfony\Contracts\EventDispatcher\Event;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;

class JsonSerializeMethodEvent extends Event
{
    private ClassName $className;
    private ClassType $classType;
    private Method $method;
    private array $jsonProperties;

    public function __construct(ClassName $className, ClassType $class, Method $method)
    {
        $this->className = $className;
        $this->classType = $class;
        $this->method = $method;
        $this->jsonProperties = [];
    }

    public function addJsonProperty(string $propertyName, string $valueCode): void
    {
        $this->jsonProperties[$propertyName] = $valueCode;
    }

    public function jsonProperties(): array
    {
        return $this->jsonProperties;
    }

    public function className(): ClassName
    {
        return $this->className;
    }

    public function classType(): ClassType
    {
        return $this->classType;
    }

    public function method(): Method
    {
        return $this->method;
    }
}
