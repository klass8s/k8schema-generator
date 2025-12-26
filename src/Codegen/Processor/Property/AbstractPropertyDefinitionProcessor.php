<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

abstract class AbstractPropertyDefinitionProcessor implements PropertyDefinitionProcessorInterface
{
    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
    }
}
