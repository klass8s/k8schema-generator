<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\DependencyInjection;

final readonly class Tags
{
    public const string DATA_TYPE_FACTORY = 'tequila.k8s_schema_generator.json_schema.data_type.factory';
    public const string TYPE_RESOLVER = 'tequila.k8s_schema_generator.type_resolver';
    public const string VO_CLASS_GENERATOR = 'tequila.k8s_schema_generator.vo_class_generator';
    public const string PROPERTY_DEFINITION_PROCESSOR = 'tequila.k8s_schema_generator.processor.property_definition';
    public const string PROPERTY_PROCESSOR = 'tequila.k8s_schema_generator.processor.property';

    private function __construct()
    {
    }
}
