<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Definition;

use Klass8s\K8SchemaGenerator\App\SemVer\VersionsService;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\TypesMap;

final readonly class ObjectDefinitionsService
{
    public function __construct(private VersionsService $versionsService)
    {
    }

    public function topLevelDefinitionsMap(TypesMap $typesMap): ApiObjectDefinitionsMap
    {
        $objectsMap = ApiObjectDefinitionsMap::fromTypesMap(
            $this->onlyObjects($typesMap)
        );

        return $this->withoutDuplicatesByKind($objectsMap);
    }

    private function onlyObjects(TypesMap $typesMap): TypesMap
    {
        return $typesMap->filter(fn (DataTypeInterface $type) =>
                $type instanceof ObjectType
                && $type->hasAnnotation(ApiObjectDefinition::ANNOTATION_GROUP_VERSION_KIND)
                && $type->hasProperty(ApiObjectDefinition::PROPERTY_METADATA));
    }

    private function withoutDuplicatesByKind(ApiObjectDefinitionsMap $definitionsMap): ApiObjectDefinitionsMap
    {
        $groupedByKind = [];
        foreach ($definitionsMap as $definition) {
            $kind = $definition->kind();
            $version = $definition->version()->string();
            $groupedByKind[$kind][$version] ??= $definition;
        }

        $newMap = [];
        foreach ($groupedByKind as $definitionsOfKind) {
            $versions = \array_map(
                fn (ApiObjectDefinition $definition) => $definition->version(),
                $definitionsOfKind
            );

            $latestStable = $this->versionsService->latestMostStable($versions);
            $chosenDefinition = $definitionsOfKind[$latestStable->string()];
            $newMap[$chosenDefinition->name()] = $chosenDefinition;
        }

        return ApiObjectDefinitionsMap::fromArray($newMap);
    }
}
