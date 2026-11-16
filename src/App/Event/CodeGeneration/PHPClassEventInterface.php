<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Event\CodeGeneration;

use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;

interface PHPClassEventInterface
{
    public function getClass(): PHPClass;
}
