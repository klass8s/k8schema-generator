<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\TypeResolver;

use Klass8s\K8SchemaGenerator\Codegen\Generator\APIClassGenerator;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Generator\DataClassGenerator;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ReferenceType;

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
