<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\Event\CodeGeneration;

use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;

interface PHPClassEventInterface
{
    public function getClass(): PHPClass;
}
