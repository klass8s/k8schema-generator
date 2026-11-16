<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor\Property;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\GeneratedClassesCache;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ReferenceType;

class ReplaceMetadataTypeProcessor extends AbstractPropertyDefinitionProcessor
{
    public function __construct(
        private readonly ?string $metadataClass,
        private readonly GeneratedClassesCache $cache,
    ) {
    }

    public function process(
        PropertyDefinition $property,
        ClassName $className,
        ClassType $class,
        Context $context
    ): void {
        $metadataClass = $this->metadataClass;
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
        return 'metadata' === $property->name()
            && $property->type() instanceof ReferenceType
            && null !== $this->metadataClass
            && !str_ends_with($className->shortName(), 'List');
    }
}
