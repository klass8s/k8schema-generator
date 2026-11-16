<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\AbstractPHPClassEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;

class ListClassesSubscriber implements EventSubscriberInterface
{
    public function onClassGenerated(AbstractPHPClassEvent $event): void
    {
        $class = $event->getClass()->classType();

        if (!str_ends_with($class->getName(), 'List') || !$class->hasProperty('items')) {
            return;
        }

        $class
            ->removeMethod('getItems')
            ->removeMethod('setItems');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGeneratedEvent::class => 'onClassGenerated',
            DataClassGeneratedEvent::class => 'onClassGenerated',
        ];
    }
}
