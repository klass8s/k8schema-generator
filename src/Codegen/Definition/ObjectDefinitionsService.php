<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Definition;

use Tequila\K8S\SchemaGenerator\App\SemVer\VersionsService;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\ObjectType;
use Tequila\K8S\SchemaGenerator\JsonSchema\TypesMap;

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
