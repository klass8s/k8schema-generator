<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Processor\Property;

use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

class MetadataPropertiesProcessor extends AbstractPropertyDefinitionProcessor
{
    private const string DESCRIPTION_PATTERN_TO_MATCH = '/populated\s+by\s+(the\s+)?system/i';

    public function process(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): void
    {
        $property->skip();
    }

    public function supports(PropertyDefinition $property, ClassName $className, ClassType $class, Context $context): bool
    {
        return 'ObjectMeta' === $className->shortName()
            && null !== $property->description()
            && 0 !== preg_match(self::DESCRIPTION_PATTERN_TO_MATCH, $property->description());
    }
}
