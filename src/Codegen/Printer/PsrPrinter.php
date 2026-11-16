<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\Printer;

use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\Printer;

class PsrPrinter extends Printer
{
    public string $indentation = '    ';

    public int $linesBetweenMethods = 1;

    public function printType(?string $type, bool $nullable = false, ?PhpNamespace $namespace = null): string
    {
        if (!$type) {
            return '';
        }

        $types = explode('|', $type);
        $types = array_flip($types);

        if ($nullable) {
            $types['null'] = count($types);
        } else {
            unset($types['null']);
        }

        return implode('|', array_keys($types));
    }
}
