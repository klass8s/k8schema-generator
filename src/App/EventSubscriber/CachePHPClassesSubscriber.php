<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGeneratedEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\ClassGenerationEventInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\ClassInProgressEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGeneratedEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\PHPClassEventInterface;
use Klass8s\K8SchemaGenerator\Codegen\GeneratedClassesCache;

final readonly class CachePHPClassesSubscriber implements EventSubscriberInterface
{
    public function __construct(private GeneratedClassesCache $cache)
    {
    }

    public function onClassGeneration(ClassGenerationEventInterface $event): void
    {
        $className = $event->className();
        $fcqn = $className->fqcn();
        if ($event->hasClass()) {
            $this->cache->set($fcqn, $event->getClass());
        }
        if ($this->cache->has($fcqn)) {
            $event->setClass($this->cache->get($fcqn));
        }
    }

    public function onClassGenerated(PHPClassEventInterface $event): void
    {
        $class = $event->getClass();
        $this->cache->set(
            $class->name()->fqcn(),
            $class
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGenerationEvent::class => 'onClassGeneration',
            DataClassGenerationEvent::class => 'onClassGeneration',
            ClassInProgressEvent::class => 'onClassGenerated',
            APIClassGeneratedEvent::class => 'onClassGenerated',
            DataClassGeneratedEvent::class => 'onClassGenerated',
        ];
    }
}
