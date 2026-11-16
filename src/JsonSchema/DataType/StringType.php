<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\JsonSchema\DataType;

final readonly class StringType extends AbstractType
{
    public const string FORMAT_RAW = 'raw'; // pseudo type
    public const string FORMAT_BYTES = 'bytes';
    public const string FORMAT_DATETIME = 'date-time';
    public const string FORMAT_EMAIL = 'email';
    public const string FORMAT_HOSTNAME = 'hostname';
    public const string FORMAT_IPV4 = 'ipv4';
    public const string FORMAT_IPV6 = 'ipv6';
    public const string FORMAT_URI = 'uri';

    public function format(): string
    {
        return $this->schema['format'] ?? self::FORMAT_RAW;
    }
}
