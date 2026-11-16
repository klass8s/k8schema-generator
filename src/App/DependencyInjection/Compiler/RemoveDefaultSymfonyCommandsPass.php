<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Klass8s\K8SchemaGenerator\App\Console\Command\GenerateCommand;
use Klass8s\K8SchemaGenerator\App\Console\Command\SchemaFetchCommand;
use Klass8s\K8SchemaGenerator\App\Console\Command\SchemaVersionsCommand;

class RemoveDefaultSymfonyCommandsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('console.command_loader');
        $definition->replaceArgument(1, [
            'generate' => GenerateCommand::class,
            'schema:fetch' => SchemaFetchCommand::class,
            'schema:versions' => SchemaVersionsCommand::class,
        ]);
    }
}
