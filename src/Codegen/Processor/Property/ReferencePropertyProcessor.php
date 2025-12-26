<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ReferenceType;

final class ReferencePropertyProcessor extends AbstractPropertyProcessor
{
    public function __construct(private readonly GeneratedClassesCache $cache)
    {
    }

    public function process(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $classType, Context $context): void
    {
        $fqcn = $property->getType();

        if (!$this->cache->has($fqcn)) {
            return;
        }

        $phpClass = $this->cache->get($fqcn);

        if ($phpClass->isExternal) {
            return;
        }

        if (!$phpClass->classType()->hasMethod('__construct')) {
            return;
        }

        $method = $phpClass->classType()->getMethod('__construct');
        if (0 !== count($method->getParameters())) {
            return;
        }

        $definition->setNullable(false);
        $property
            ->setNullable(false)
            ->setInitialized(false);
        $initializer = function (Property $property, Method $constructor): void {
            $propertyType = $property->getType();
            $propertyValue = sprintf('new %s()', $propertyType);

            $constructor->addBody(
                sprintf('$this->%s = %s;', $property->getName(), $propertyValue)
            );
        };

        $definition->setInitializer($initializer);
    }

    public function supports(PropertyDefinition $definition, Property $property, ClassName $className, ClassType $class, Context $context): bool
    {
        return $definition->type() instanceof ReferenceType;
    }
}
