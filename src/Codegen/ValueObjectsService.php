<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen;

use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\Codegen\ValueObject\VOClassGeneratorInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

final readonly class ValueObjectsService
{
    /**
     * @param VOClassGeneratorInterface[] $generators
     */
    public function __construct(
        private PHPTypesService $phpTypesService,
        private GeneratedClassesCache $cache,
        private iterable $generators,
    ) {
    }

    public function generateVOClass(string $definitionName, DataTypeInterface $type, Context $context): PHPClass
    {
        $className = $this->createClassName($definitionName, $context);
        if ($this->cache->has($className->fqcn())) {
            return $this->cache->get($className->fqcn());
        }
        foreach ($this->generators as $generator) {
            if ($generator->supports($className, $type)) {
                $phpClass = $generator->generate($className, $type, $context, $this->phpTypesService);
                $this->cache->set($className->fqcn(), $phpClass);

                return $phpClass;
            }
        }

        throw new \LogicException(sprintf('There are no generators able to create VO class for definition "%s" with type "%s"', $definitionName, $type::class));
    }

    private function createClassName(string $definitionName, Context $context): ClassName
    {
        $namespaceName = $context->namespacePrefix.'\\ValueObject';

        return ClassName::fromDefinitionName($namespaceName, $definitionName);
    }
}
