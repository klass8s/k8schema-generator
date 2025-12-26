<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration;

use Symfony\Contracts\EventDispatcher\Event;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;

class ClassNameEvent extends Event
{
    private ClassName $className;

    public function __construct(ClassName $className)
    {
        $this->className = $className;
    }

    public function getClassName(): ClassName
    {
        return $this->className;
    }

    public function setClassName(ClassName $className): void
    {
        $this->className = $className;
    }
}
