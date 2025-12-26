<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\DataClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

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
