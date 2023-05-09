<?php

declare(strict_types=1);

namespace Eniams\SafeMigrationsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SafeMigrationsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        $this->processConfiguration($configuration, $configs);
    }
}