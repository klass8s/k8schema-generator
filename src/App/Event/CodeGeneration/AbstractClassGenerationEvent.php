<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Symfony\Contracts\EventDispatcher\Event;
use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

abstract class AbstractClassGenerationEvent extends Event implements ClassGenerationEventInterface
{
    protected ?PHPClass $class = null;

    public function __construct(
        protected readonly ClassName $className,
        protected readonly Context $context,
    ) {
        $this->class = null;
    }

    public function className(): ClassName
    {
        return $this->className;
    }

    public function context(): Context
    {
        return $this->context;
    }

    public function hasClass(): bool
    {
        return null !== $this->class;
    }

    public function getClass(): PHPClass
    {
        return $this->class;
    }

    public function setClass(PHPClass $class): void
    {
        $this->class = $class;
    }
}
