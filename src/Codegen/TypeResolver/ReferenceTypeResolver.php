<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Tequila\K8S\SchemaGenerator\Codegen\Generator\APIClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\DataClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ReferenceType;

class ReferenceTypeResolver extends AbstractTypeResolver
{
    public function __construct(
        private readonly APIClassGenerator $apiClassGenerator,
        private readonly DataClassGenerator $dataClassGenerator,
    ) {
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof ReferenceType;
    }

    public function resolve(
        DataTypeInterface|ReferenceType $type,
        PHPTypesService $service,
        Context $context,
        bool $nullable,
        array $runtimeParams,
    ): PHPType {
        $definitions = $context->definitions;
        $types = $context->types;

        $definitionName = $type->referencedDefinitionName();

        if ($definitions->has($definitionName)) {
            $definition = $definitions->get($definitionName);
            $class = $this->apiClassGenerator->generate($definition, $context);
        } elseif ($types->has($definitionName)) {
            $type = $types->get($definitionName);
            if (!$type instanceof ObjectType) {
                return $service->resolveType($type, $context, $nullable);
            }
            $class = $this->dataClassGenerator->generateFromDefinitionName($definitionName, $type, $context);
        } else {
            throw new \LogicException(sprintf('Unknown definition name "%s".', $definitionName));
        }

        return new PHPType($class->name()->fqcn(), $class->name()->fqcn(), $nullable);
    }
}
