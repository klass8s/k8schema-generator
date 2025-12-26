<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;

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
