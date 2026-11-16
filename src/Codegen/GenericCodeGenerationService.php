<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen;

use Klass8s\K8SchemaGenerator\App\Util\ClassUtil;
use Klass8s\K8SchemaGenerator\Codegen\Definition\ApiObjectDefinitionsMap;
use Klass8s\K8SchemaGenerator\Codegen\Generator\DataClassGenerator;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\ObjectType;
use Klass8s\K8SchemaGenerator\JsonSchema\JsonSchemaService;

final readonly class GenericCodeGenerationService implements CodeGenerationServiceInterface
{
    public function __construct(
        private JsonSchemaService $jsonSchemaService,
        private DataClassGenerator $classGenerator,
        private GeneratedClassesCache $cache,
    ) {
    }

    public function generate(
        array $jsonSchema,
        string $namespacePrefix,
        string $rootDir,
        ?ClassName $resourceInterface = null,
        ?ClassName $resourceListInterface = null
    ): void {
        $typesMap = $this->jsonSchemaService->typesMap($jsonSchema);
        $context = ContextBuilder::instance()
            ->setDefinitions(ApiObjectDefinitionsMap::fromArray([])) // dummy call, definitions are not used
            ->setTypes($typesMap)
            ->setOutputDir($rootDir)
            ->setNamespacePrefix($namespacePrefix)
            ->setResourceInterface(ClassName::fromFQCN('Dummy')) // not generated
            ->setResourceListInterface(ClassName::fromFQCN('Dummy')) // not generated
            ->build();

        $definitions = $typesMap
            ->filter(fn (DataTypeInterface $type) => $type instanceof ObjectType)
            ->getIterator();

        foreach ($definitions as $name => $definition) {
            $this->classGenerator->generateFromDefinitionName($name, $definition, $context);
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
}
