<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;

class DataClassGenerationEvent extends AbstractClassGenerationEvent
{
    private ObjectType $objectType;

    public function __construct(ClassName $className, Context $context, ObjectType $objectType)
    {
        $this->objectType = $objectType;

        parent::__construct($className, $context);
    }

    public function objectType(): ObjectType
    {
        return $this->objectType;
    }
}
