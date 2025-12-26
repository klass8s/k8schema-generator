<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration;

use Tequila\K8S\SchemaGenerator\Codegen\Context;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;

interface ClassGenerationEventInterface
{
    public function className(): ClassName;
    public function context(): Context;
    public function hasClass(): bool;
    public function getClass(): PHPClass;
    public function setClass(PHPClass $class): void;
}
