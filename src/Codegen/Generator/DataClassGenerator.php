<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\ClassGenerationEventInterface;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\DataClassGenerationEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\PHPClassEventInterface;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;

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
