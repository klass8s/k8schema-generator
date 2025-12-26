<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\TypeResolver;

use Nette\PhpGenerator\InterfaceType;
use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;
use Tequila\K8S\SchemaGenerator\Codegen\PHPTypesService;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\StringType;

final class DateTimeResolver extends AbstractTypeResolver
{
    private const string GENERATED_CLASS_NAME = 'DateTimeInterface';

    public function __construct(private readonly GeneratedClassesCache $cache)
    {
    }

    public function resolve(DataTypeInterface $type, PHPTypesService $service, Context $context, bool $nullable, array $runtimeParams): PHPType
    {
        $className = ClassName::fromNamespaceAndName($context->namespacePrefix, self::GENERATED_CLASS_NAME);
        $fqcn = $className->fqcn();
        if (!$this->cache->has($fqcn)) {
            $classType = new InterfaceType($className->shortName());
            $classType
                ->addExtend('\\'.\DateTimeInterface::class)
                ->addExtend(\JsonSerializable::class);

            $phpClass = new PHPClass($className, $classType);
            $phpClass->useClass(ClassName::fromFQCN(\JsonSerializable::class));

            $this->cache->set($fqcn, $phpClass);
        }

        return new PHPType($fqcn, $fqcn, $nullable);
    }

    public function supports(DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME === $type->format();
    }
}
