<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\APIClassGenerationEvent;
use Klass8s\K8SchemaGenerator\App\Event\CodeGeneration\JsonSerializeMethodEvent;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;

class SetKindAndVersionSubscriber implements EventSubscriberInterface
{
    use ProcessedClassesTrait;

    private const string PROPERTY_API_VERSION = 'apiVersion';
    private const string PROPERTY_KIND = 'kind';
    private const string CONSTANT_API_VERSION = 'API_VERSION';
    private const string CONSTANT_KIND = 'KIND';

    private array $map = [];

    public function onAPIClassGeneration(APIClassGenerationEvent $event): void
    {
        $className = $event->className();
        $definition = $event->definition();
        if ($this->isProcessed($className)) {
            return;
        }

        $properties = $definition->properties();
        $properties[self::PROPERTY_API_VERSION]->skip();
        $properties[self::PROPERTY_KIND]->skip();

        $this->map[$className->fqcn()] = [
            self::PROPERTY_API_VERSION => $definition->apiVersion(),
            self::PROPERTY_KIND => $definition->kind(),
        ];

        $this->markAsProcessed($className);
    }

    public function onJsonSerializeMethodGeneration(JsonSerializeMethodEvent $event): void
    {
        $className = $event->className();
        if (!array_key_exists($className->fqcn(), $this->map)) {
            return;
        }

        $class = $event->classType();
        $class
            ->addConstant(
                self::CONSTANT_API_VERSION,
                $this->apiVersionForClass($className)
            )
            ->setType('string');
        $class
            ->addConstant(self::CONSTANT_KIND, $this->kindForClass($className))
            ->setType('string');

        $event->addJsonProperty(
            'apiVersion',
            sprintf('self::%s', self::CONSTANT_API_VERSION)
        );
        $event->addJsonProperty(
            'kind',
            sprintf('self::%s', self::CONSTANT_KIND)
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            APIClassGenerationEvent::class => 'onAPIClassGeneration',
            JsonSerializeMethodEvent::class => 'onJsonSerializeMethodGeneration',
        ];
    }

    private function apiVersionForClass(ClassName $className)
    {
        return $this->map[$className->fqcn()][self::PROPERTY_API_VERSION];
    }

    private function kindForClass(ClassName $className)
    {
        return $this->map[$className->fqcn()][self::PROPERTY_KIND];
    }
}
