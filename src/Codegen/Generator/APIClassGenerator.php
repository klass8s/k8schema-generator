<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\APIClassGenerationEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\ClassGenerationEventInterface;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\PHPClassEventInterface;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Definition\ApiObjectDefinition;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;

class APIClassGenerator extends AbstractGenerator
{
    private APIClassGenerationEvent $generationEvent;

    public function generate(ApiObjectDefinition $definition, Context $context): PHPClass
    {
        $className = ClassName::fromDefinitionName($context->namespacePrefix, $definition->name());
        $this->generationEvent = new APIClassGenerationEvent($className, $context, $definition);

        return $this->doGenerate(
            $className,
            $definition->description(),
            $definition->properties(),
            $context
        );
    }

    protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName
    {
        if (!$phpClass->classType()->hasMethod('metadata')) {
            throw new \LogicException(sprintf('Method metadata() not found in class "%s"', $phpClass->name()->fqcn()));
        }

        $returnType = $phpClass->classType()->getMethod('metadata')->getReturnType();
        $metaClassName = ClassName::fromFQCN($returnType)->shortName();

        if ('ObjectMeta' === $metaClassName) {
            return $context->resourceInterface;
        } elseif ('ListMeta' === $metaClassName) {
            return $context->resourceListInterface;
        }

        throw new \LogicException(sprintf('Unknown return type "%s" for %s::metadata() method', $returnType, $phpClass->name()->fqcn()));
    }

    protected function createGenerationEvent(): ClassGenerationEventInterface
    {
        return $this->generationEvent;
    }

    protected function createPHPClassEvent(PHPClass $class): PHPClassEventInterface
    {
        return new APIClassGeneratedEvent($class);
    }
}
