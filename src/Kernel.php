<?php

declare(strict_types=1);

namespace Tequila\K8S\SchemaGenerator;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Tequila\K8S\SchemaGenerator\App\DependencyInjection\Compiler\RemoveDefaultSymfonyCommandsPass;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new RemoveDefaultSymfonyCommandsPass(),
            type: PassConfig::TYPE_BEFORE_REMOVING,
            priority: -128
        );
    }
}
