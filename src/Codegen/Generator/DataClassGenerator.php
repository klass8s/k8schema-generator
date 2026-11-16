<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Generator;

use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\ClassGenerationEventInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\PHPClassEventInterface;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;

final class DataClassGenerator extends AbstractGenerator
{
    private DataClassGenerationEvent $generationEvent;

    public function generateFromDefinitionName(string $definitionName, ObjectType $type, Context $context): PHPClass
    {
        $className = ClassName::fromDefinitionName($context->namespacePrefix, $definitionName);

        return $this->generateFromClassName($className, $type, $context);
    }

    public function generateFromClassName(ClassName $className, ObjectType $type, Context $context): PHPClass
    {
        $this->generationEvent = new DataClassGenerationEvent($className, $context, $type);

        return $this->doGenerate(
            $className,
            $type->description(),
            $type->properties(),
            $context,
        );
    }

    protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName
    {
        return ClassName::fromFQCN(\JsonSerializable::class);
    }

    protected function createGenerationEvent(): ClassGenerationEventInterface
    {
        return $this->generationEvent;
    }

    protected function createPHPClassEvent(PHPClass $class): PHPClassEventInterface
    {
        return new DataClassGeneratedEvent($class);
    }
}
