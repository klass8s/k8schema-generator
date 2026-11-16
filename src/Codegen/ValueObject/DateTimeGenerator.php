<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\Codegen\ValueObject;

use Klass8s\K8SchemaGenerator\Codegen\Context;
use Klass8s\K8SchemaGenerator\Codegen\PHPTypesService;
use Klass8s\K8SchemaGenerator\Codegen\Type\ClassName;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPClass;
use Klass8s\K8SchemaGenerator\Codegen\Type\PHPType;
use Nette\PhpGenerator\ClassType;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\DataTypeInterface;
use Klass8s\K8SchemaGenerator\JsonSchema\DataType\StringType;

class DateTimeGenerator extends AbstractGenerator
{
    private const string VALUE_TYPE = \DateTimeInterface::class;
    private const string DATE_TIME_FORMAT = 'c';

    private PHPType $phpType;

    public function __construct()
    {
        $this->phpType = new PHPType(self::VALUE_TYPE, self::VALUE_TYPE, false);
    }

    public function generate(ClassName $className, DataTypeInterface|StringType $type, Context $context, PHPTypesService $service): PHPClass
    {
        $class = new ClassType($className->shortName());

        $this
            ->defineProperty($class, $this->phpType)
            ->defineConstructor($class, $this->phpType)
            ->defineToStringMethod($class)
            ->defineFactoryMethod($class, $this->phpType, 'fromDateTime', 'dateTime');

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\DateTimeInterface::class));
        $this->defineJsonSerializeMethod($phpClass);

        return $phpClass;
    }

    public function supports(ClassName $className, DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME === $type->format();
    }

    private function defineToStringMethod(ClassType $class): self
    {
        $method = $class->addMethod('toString');
        $method
            ->setPublic()
            ->setReturnType('string')
            ->setReturnNullable(false)
            ->addBody(
                sprintf(
                    'return $this->%s->format(\'%s\');',
                    self::VALUE_PROPERTY_NAME,
                    self::DATE_TIME_FORMAT
                )
            );

        return $this;
    }

    protected function jsonSerializeBody(): string
    {
        return 'return $this->toString();';
    }
}
