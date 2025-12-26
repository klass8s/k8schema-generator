<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

final class CRDMetadataPropertyProcessor extends AbstractPropertyDefinitionProcessor
{
    public function __construct(
        private readonly ?string $crdMetadataClass,
        private readonly GeneratedClassesCache $cache,
    ) {
    }

    public function process(
        PropertyDefinition $property,
        ClassName $className,
        ClassType $class,
        Context $context
    ): void {
        $metadataClass = $this->crdMetadataClass;
        $className = ClassName::fromFQCN($metadataClass);

        $property->setNullable(false);
        $property->phpType = new PHPType($metadataClass, $metadataClass, false);

        $initializer = function (Property $property, Method $constructor) use ($className): void {
            $property
                ->setNullable(false)
                ->setInitialized(false);

            $propertyValue = sprintf('new %s()', $className->shortName());

            $constructor->addBody(
                sprintf('$this->%s = %s;', $property->getName(), $propertyValue)
            );
        };

        $property->setInitializer($initializer);

        $phpClass = new PHPClass($className, new ClassType(), true);
        $this->cache->set($metadataClass, $phpClass);
    }

    public function supports(
        PropertyDefinition $property,
        ClassName $className,
        ClassType $class,
        Context $context
    ): bool {
        // `metadata` property has inlined "object" type (not reference type) only in CRDs
        return 'metadata' === $property->name() && $property->type() instanceof ObjectType && null !== $this->crdMetadataClass;
    }
}
