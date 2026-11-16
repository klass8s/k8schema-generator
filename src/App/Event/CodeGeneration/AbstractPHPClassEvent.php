<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

abstract class AbstractPHPClassEvent implements PHPClassEventInterface
{
    public function __construct(private readonly PHPClass $class)
    {
    }

    public function getClass(): PHPClass
    {
        return $this->class;
    }
}
