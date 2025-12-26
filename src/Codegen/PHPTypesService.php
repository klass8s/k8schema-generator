<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen;

use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPType;
use Tequila\K8S\SchemaGenerator\Codegen\TypeResolver\TypeResolverInterface;
use Tequila\K8S\SchemaGenerator\JsonSchema\DataType\DataTypeInterface;

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
