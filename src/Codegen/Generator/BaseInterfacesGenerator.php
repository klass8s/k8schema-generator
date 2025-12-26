<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\Codegen\Generator;

use Nette\PhpGenerator\InterfaceType;
use Tequila\K8S\SchemaGenerator\Codegen\Type\ClassName;
use Tequila\K8S\SchemaGenerator\Codegen\Type\PHPClass;

class BaseInterfacesGenerator
{
    private const string API_RESOURCE_INTERFACE = 'APIResourceInterface';
    private const string API_RESOURCE_LIST_INTERFACE = 'APIResourceListInterface';
    private const string OBJECT_META = 'Apimachinery\\Pkg\\Apis\\Meta\\V1\\ObjectMeta';
    private const string LIST_META = 'Apimachinery\Pkg\Apis\Meta\V1\\ListMeta';

    public function resourceInterface(string $namespacePrefix): PHPClass
    {
        return $this->generate(
            $namespacePrefix,
            self::API_RESOURCE_INTERFACE,
            self::OBJECT_META
        );
    }

    public function resourceListInterface(string $namespacePrefix): PHPClass
    {
        return $this->generate(
            $namespacePrefix,
            self::API_RESOURCE_LIST_INTERFACE,
            self::LIST_META
        );
    }

    private function generate(string $namespacePrefix, string $className, string $metadataClass): PHPClass
    {
        $className = ClassName::fromNamespaceAndName(
            $namespacePrefix,
            $className
        );

        $class = new InterfaceType($className->shortName());
        $class->addExtend(\JsonSerializable::class);

        $metadataClass = $namespacePrefix.'\\'.$metadataClass;
        $class
            ->addMethod('metadata')
            ->setReturnType($metadataClass)
            ->setReturnNullable(false);

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\JsonSerializable::class));
        $phpClass->useClass(ClassName::fromFQCN($metadataClass));

        return $phpClass;
    }
}
