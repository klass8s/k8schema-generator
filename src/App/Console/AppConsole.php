<?php

declare(strict_types=1);

namespace Klass8s\K8SchemaGenerator\App\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Klass8s\K8SchemaGenerator\App\Console\Command\GenerateCommand;

class AppConsole extends Application
{
    protected function getDefaultCommands(): array
    {
        return [new HelpCommand(), new ListCommand()];
    }
}
