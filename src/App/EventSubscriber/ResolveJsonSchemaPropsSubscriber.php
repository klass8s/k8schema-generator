<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Nette\PhpGenerator\InterfaceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\DataClassGenerationEvent;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

class ResolveJsonSchemaPropsSubscriber implements EventSubscriberInterface
{
    private const string CLASS_NAME = 'JSONSchemaProps';
    private const int PRIORITY = 2;

    public function onDataClassGeneration(DataClassGenerationEvent $event): void
    {
        $className = $event->className();
        if (self::CLASS_NAME !== $className->shortName()) {
            return;
        }

        $class = new InterfaceType($className->shortName());

        $phpClass = new PHPClass($className, $class);
        $event->setClass($phpClass);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataClassGenerationEvent::class => ['onDataClassGeneration', self::PRIORITY],
        ];
    }
}
