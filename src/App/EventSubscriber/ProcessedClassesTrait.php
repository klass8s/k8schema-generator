<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\EventSubscriber;

use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;

trait ProcessedClassesTrait
{
    private array $processedClasses = [];

    private function isProcessed(ClassName $className): bool
    {
        return array_key_exists($className->fqcn(), $this->processedClasses);
    }

    private function markAsProcessed(ClassName $className): void
    {
        $this->processedClasses[$className->fqcn()] = null;
    }
}
