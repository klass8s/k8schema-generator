<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\ListClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\MapClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ArrayType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\UnknownType;

final readonly class CollectionResolver implements TypeResolverInterface
{
    public function __construct(
        private MapClassGenerator $mapGenerator,
        private ListClassGenerator $listGenerator,
    ) {
    }

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $itemPHPType = $this->resolveItemType($type, $service, $context, $runtimeParams);
        $generator = $type instanceof ObjectType ? $this->mapGenerator : $this->listGenerator;
        $phpClass = $generator->generate($itemPHPType, $context);
        $className = $phpClass->name();

        return new PHPType($className->fqcn(), $className->fqcn(), false);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return ($type instanceof ObjectType && 0 === \count($type->properties()))
               || $type instanceof ArrayType;
    }

    protected function resolveItemType(DataTypeInterface $type, PHPTypesService $service, Context $context, array $runtimeParams): PHPType
    {
        if ($type instanceof ObjectType) {
            $itemType = $type->additionalPropertiesType();
        } elseif ($type instanceof ArrayType) {
            $itemType = $type->itemType();
        } else {
            throw new \LogicException(sprintf('%s expects either %s or %s, %s given.', __CLASS__, ObjectType::class, ArrayType::class, $type::class));
        }

        if (null === $itemType) {
            $itemType = new UnknownType();
        }

        /** @var ClassName $parentClassName */
        $parentClassName = $runtimeParams['className'];
        /** @var PropertyDefinition $propertyDefinition */
        $propertyDefinition = $runtimeParams['propertyDefinition'];

        $shortName = $parentClassName->shortName().ucwords($propertyDefinition->name());
        $className = ClassName::fromNamespaceAndName($parentClassName->namespace(), $shortName);

        $itemPropertyDefinition = new PropertyDefinition('type', $itemType, false);
        $itemRuntimeParams = ['className' => $className, 'propertyDefinition' => $itemPropertyDefinition];

        return $service->resolveType($itemType, $context, false, $itemRuntimeParams);
    }
}
