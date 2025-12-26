<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\ValueObject;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\OneOfType;

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
