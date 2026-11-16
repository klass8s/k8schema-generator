<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGenerationEvent;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\PropertyDefinition;

class MetadataAndSpecSubscriber implements EventSubscriberInterface
{
    private const string PROPERTY_METADATA = 'metadata';
    private const string PROPERTY_SPEC = 'spec';

    public function onAPIClassGeneration(APIClassGenerationEvent $event): void
    {
        $properties = $event->definition()->properties();
        $this->requireMetaAndSpec($properties);
    }

    public function onDataClassGeneration(DataClassGenerationEvent $event): void
    {
        $properties = $event->objectType()->properties();
        $this->requireMetaAndSpec($properties);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGenerationEvent::class => 'onAPIClassGeneration',
            DataClassGenerationEvent::class => 'onDataClassGeneration',
        ];
    }

    /**
     * @param array<string, PropertyDefinition> $properties
     */
    private function requireMetaAndSpec(array $properties): void
    {
        if (array_any([self::PROPERTY_METADATA, self::PROPERTY_SPEC], fn (string $propertyName): bool => !array_key_exists($propertyName, $properties))) {
            return;
        }

        $properties[self::PROPERTY_METADATA]->setRequired(true);
        $properties[self::PROPERTY_SPEC]->setRequired(true);
    }
}
