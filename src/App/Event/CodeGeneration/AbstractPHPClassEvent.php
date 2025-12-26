<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration;

use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;

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
