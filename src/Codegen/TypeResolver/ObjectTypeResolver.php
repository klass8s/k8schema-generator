<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\TypeResolver;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Generator\DataClassGenerator;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

final class ObjectTypeResolver extends AbstractTypeResolver
{
    public function __construct(private readonly DataClassGenerator $dataClassGenerator)
    {
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof ObjectType && 0 < count($type->properties());
    }

    public function resolve(
        DataTypeInterface|ObjectType $type,
        PHPTypesService $service,
        Context $context,
        bool $nullable,
        array $runtimeParams,
    ): PHPType {
        /** @var ClassName $parentClassName */
        $parentClassName = $runtimeParams['className'];
        /** @var PropertyDefinition $propertyDefinition */
        $propertyDefinition = $runtimeParams['propertyDefinition'];

        $shortName = $parentClassName->shortName().ucwords($propertyDefinition->name());
        $className = ClassName::fromNamespaceAndName($parentClassName->namespace(), $shortName);

        $class = $this->dataClassGenerator->generateFromClassName($className, $type, $context);

        return new PHPType($class->name()->fqcn(), $class->name()->fqcn(), $nullable);
    }
}
