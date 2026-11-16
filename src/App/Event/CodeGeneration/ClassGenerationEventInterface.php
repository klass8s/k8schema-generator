<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

interface ClassGenerationEventInterface
{
    public function className(): ClassName;
    public function context(): Context;
    public function hasClass(): bool;
    public function getClass(): PHPClass;
    public function setClass(PHPClass $class): void;
}
