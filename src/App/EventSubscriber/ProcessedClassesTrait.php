<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;

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
