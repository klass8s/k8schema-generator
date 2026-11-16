<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Generator;

use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\ClassGenerationEventInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\PHPClassEventInterface;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Definition\ApiObjectDefinition;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

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
            $context,
        );
    }

    protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName
    {
        if ($phpClass->classType()->hasMethod('metadata')) {
            $method = $phpClass->classType()->getMethod('metadata');
        } elseif (!$phpClass->classType()->hasMethod('getMetadata')) {
            throw new \LogicException(sprintf('Neither method `metadata()`, not method `getMetadata()` was found in class "%s".', $phpClass->name()->fqcn()));
        } else {
            $method = $phpClass->classType()->getMethod('getMetadata');
        }

        $returnType = $method->getReturnType();
        $metaClassName = ClassName::fromFQCN($returnType)->shortName();

        if ('ObjectMeta' === $metaClassName) {
            return $context->resourceInterface;
        } elseif ('ListMeta' === $metaClassName) {
            return $context->resourceListInterface;
        }

        throw new \LogicException(sprintf('Unknown return type "%s" for %s::%s() method.', $returnType, $phpClass->name()->fqcn(), $method->getName()));
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
