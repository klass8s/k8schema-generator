<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\AbstractPHPClassEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;

final readonly class ObjectPropertyFluentGetterSubscriber implements EventSubscriberInterface
{
    public function __construct(private GeneratedClassesCache $cache)
    {
    }

    public function onClassGenerated(AbstractPHPClassEvent $event): void
    {
        $phpClass = $event->getClass();
        $classType = $phpClass->classType();

        foreach ($classType->getProperties() as $property) {
            $propertyType = $property->getType();
            if (null === $propertyType || !$this->cache->has($propertyType)) {
                continue; // Not a class name
            }
            $propertyPhpClass = $this->cache->get($propertyType);
            if (!$propertyPhpClass->isExternal) {
                $propertyClass = $propertyPhpClass->classType();
                if (!$propertyClass->hasMethod('__construct')) {
                    continue;
                }
                $constructor = $propertyClass->getMethod('__construct');
                if (0 !== count($constructor->getParameters())) {
                    continue;
                }
            }

            $propertyName = $property->getName();
            $setterName = 'set'.ucfirst($propertyName);
            $getterName = 'get'.ucfirst($propertyName);

            $fluentGetterName = $propertyName;
            if ($classType->hasMethod($fluentGetterName)) {
                continue;
            }

            $fluentGetter = $classType->addMethod($fluentGetterName);
            $fluentGetter
                ->setReturnNullable(false)
                ->setReturnType($propertyType)
            ;

            if ($property->isNullable()) {
                $fluentGetter->setBody(
                    sprintf('return $this->%s ??= new %s();', $propertyName, $propertyType)
                );
            } else {
                $fluentGetter->setBody(sprintf('return $this->%s;', $propertyName));
                $classType->removeMethod($getterName);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGeneratedEvent::class => 'onClassGenerated',
            DataClassGeneratedEvent::class => 'onClassGenerated',
        ];
    }
}
