<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Definition\ApiObjectDefinition;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;

class APIClassGenerationEvent extends AbstractClassGenerationEvent
{
    private ApiObjectDefinition $definition;

    public function __construct(ClassName $className, Context $context, ApiObjectDefinition $definition)
    {
        $this->definition = $definition;

        parent::__construct($className, $context);
    }

    public function definition(): ApiObjectDefinition
    {
        return $this->definition;
    }
}
