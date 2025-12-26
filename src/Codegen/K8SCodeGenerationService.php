<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen;

use Tequila\K8S\SchemaGenerator\App\Util\ClassUtil;
use Tequila\K8S\SchemaGenerator\Codegen\Definition\ObjectDefinitionsService;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\APIClassGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\Generator\BaseInterfacesGenerator;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\JsonSchema\JsonSchemaService;

final readonly class K8SCodeGenerationService implements CodeGenerationServiceInterface
{
    public function __construct(
        private ObjectDefinitionsService $definitionsService,
        private JsonSchemaService $jsonSchemaService,
        private APIClassGenerator $classGenerator,
        private BaseInterfacesGenerator $interfacesGenerator,
        private GeneratedClassesCache $cache,
    ) {
    }

    public function generate(array $jsonSchema, string $namespacePrefix, string $rootDir, ?ClassName $resourceInterface = null, ?ClassName $resourceListInterface = null): void
    {
        $typesMap = $this->jsonSchemaService->typesMap($jsonSchema);
        $definitionsMap = $this->definitionsService->topLevelDefinitionsMap($typesMap);
        $resourceInterface = $resourceInterface ?: $this->resourceInterface($namespacePrefix)->name();
        $resourceListInterface = $resourceListInterface ?: $this->resourceListInterface($namespacePrefix)->name();
        $context = ContextBuilder::instance()
            ->setDefinitions($definitionsMap)
            ->setTypes($typesMap)
            ->setOutputDir($rootDir)
            ->setNamespacePrefix($namespacePrefix)
            ->setResourceInterface($resourceInterface)
            ->setResourceListInterface($resourceListInterface)
            ->build();

        foreach ($context->definitions as $definition) {
            $this->classGenerator->generate($definition, $context);
        }

        foreach ($this->cache as $fqcn => $class) {
            /** @var PHPClass $class */
            if ($class->isExternal) {
                continue;
            }
            $path = ClassUtil::filePathForClass($fqcn, $context);
            file_put_contents($path, $class->toString($context));
        }
    }

    private function resourceInterface(string $namespacePrefix): PHPClass
    {
        $resourceInterface = $this->interfacesGenerator->resourceInterface(
            $namespacePrefix
        );
        $this->cache->set($resourceInterface->name()->fqcn(), $resourceInterface);

        return $resourceInterface;
    }

    private function resourceListInterface(string $namespacePrefix): PHPClass
    {
        $resourceListInterface = $this->interfacesGenerator->resourceListInterface(
            $namespacePrefix
        );
        $this->cache->set($resourceListInterface->name()->fqcn(), $resourceListInterface);

        return $resourceListInterface;
    }
}
