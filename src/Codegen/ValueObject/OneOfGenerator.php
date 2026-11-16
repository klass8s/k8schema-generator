<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\ValueObject;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\OneOfType;

class OneOfGenerator extends AbstractGenerator
{
    public function generate(ClassName $className, DataTypeInterface|OneOfType $type, Context $context, PHPTypesService $service): PHPClass
    {
        $class = new ClassType($className->shortName());
        $phpType = $service->resolveType($type, $context, false);

        $this
            ->defineProperty($class, $phpType)
            ->defineConstructor($class, $phpType);

        foreach ($type->types() as $type) {
            $phpType = $service->resolveType($type, $context, false);
            $this->defineFactoryMethod($class, $phpType);
        }

        $phpClass = new PHPClass($className, $class);
        $this->defineJsonSerializeMethod($phpClass);

        return $phpClass;
    }

    public function supports(ClassName $className, DataTypeInterface $type): bool
    {
        return $type instanceof OneOfType;
    }
}
