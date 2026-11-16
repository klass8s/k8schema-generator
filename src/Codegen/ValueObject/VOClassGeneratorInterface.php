<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\ValueObject;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

interface VOClassGeneratorInterface
{
    public function generate(ClassName $className, DataTypeInterface $type, Context $context, PHPTypesService $service): PHPClass;
    public function supports(ClassName $className, DataTypeInterface $type): bool;
}
