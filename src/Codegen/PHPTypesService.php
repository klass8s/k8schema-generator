<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen;

use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Klass8s\K8SchemaGenerator\Codegen\TypeResolver\TypeResolverInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

final readonly class PHPTypesService
{
    /**
     * @param TypeResolverInterface[] $typeResolvers
     */
    public function __construct(private iterable $typeResolvers)
    {
    }

    public function resolveType(DataTypeInterface $type, Context $context, bool $nullable, array $runtimeParams = []): PHPType
    {
        foreach ($this->typeResolvers as $resolver) {
            if ($resolver->supports($type)) {
                return $resolver->resolve($type, $this, $context, $nullable, $runtimeParams);
            }
        }

        throw new \LogicException(sprintf('There are no resolvers for type "%s".', $type::class));
    }
}
