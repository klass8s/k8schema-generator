<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\AbstractPHPClassEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;
use Tequila\K8S\SchemaGenerator\Codegen\GeneratedClassesCache;

final readonly class RemoveObjectSettersSubscriber implements EventSubscriberInterface
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
            $phpClass = $this->cache->get($propertyType);
            if (!$phpClass->isExternal) {
                $propertyClass = $phpClass->classType();
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

            $classType->removeMethod($setterName);
            $getter = $classType->getMethod($getterName);
            $newGetterName = lcfirst(substr($getterName, 3));
            $newGetter = $getter->cloneWithName($newGetterName);
            $methods = $classType->getMethods();
            $methods[] = $newGetter;
            $classType->setMethods(array_values($methods));
            $classType->removeMethod($getterName);
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
