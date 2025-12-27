<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator\App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Tequila\K8S\SchemaGenerator\App\Console\Command\GenerateCommand;
use Tequila\K8S\SchemaGenerator\App\Console\Command\SchemaFetchCommand;
use Tequila\K8S\SchemaGenerator\App\Console\Command\SchemaVersionsCommand;

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
