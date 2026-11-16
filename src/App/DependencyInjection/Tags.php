<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\DependencyInjection;

final readonly class Tags
{
    public const string DATA_TYPE_FACTORY = 'k8schema_generator.json_schema.data_type.factory';
    public const string TYPE_RESOLVER = 'k8schema_generator.type_resolver';
    public const string VO_CLASS_GENERATOR = 'k8schema_generator.vo_class_generator';
    public const string PROPERTY_DEFINITION_PROCESSOR = 'k8schema_generator.processor.property_definition';
    public const string PROPERTY_PROCESSOR = 'k8schema_generator.processor.property';

    private function __construct()
    {
    }
}
