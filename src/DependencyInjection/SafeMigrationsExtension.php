<?php

declare(strict_types=1);

namespace Eniams\SafeMigrationsBundle\DependencyInjection;

use Eniams\SafeMigrationsBundle\Command\DebugConfigurationCommand;
use Eniams\SafeMigrationsBundle\MigrationFileSystem;
use Eniams\SafeMigrationsBundle\Statement\StatementInterface;
use Eniams\SafeMigrationsBundle\Warning\WarningFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 * @author Quentin Dequippe <quentin@dequippe.tech>
 */
class SafeMigrationsExtension extends ConfigurableExtension
{
    /** @phpstan-ignore-next-line */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        $migrationsPath = $mergedConfig['migrations_path'];
        $criticalTables = $mergedConfig['critical_tables'];
        $excludedStatements = $mergedConfig['excluded_statements'];

        if (!is_dir($migrationsPath)) {
            throw new \InvalidArgumentException(sprintf('You must provide a valid path to your migrations directory. "%s" given.', $migrationsPath));
        }

        $container->setDefinition('eniams.safe_migrations.file_system', new Definition(MigrationFileSystem::class))
            ->setArgument(0, $migrationsPath)
        ;

        $container->registerForAutoconfiguration(StatementInterface::class)->addTag('eniams.safe_migrations.statement');
        $excludedServiceStatements = $this->getExcludedStatements($container, $excludedStatements);

        $container->setDefinition('eniams.safe_migrations.warning_factory', new Definition(WarningFactory::class))
            ->setArguments([
                tagged_iterator('eniams.safe_migrations.statement'),
                $criticalTables,
                $excludedServiceStatements,
            ])
        ;

        $container->setDefinition('eniams.safe_migrations.debug_command', new Definition(DebugConfigurationCommand::class))
            ->setArguments([
                tagged_iterator('eniams.safe_migrations.statement'),
                $criticalTables,
                $excludedServiceStatements,
            ])
            ->addTag('console.command')
        ;
    }

    /**
     * @param array<string> $excludedStatements
     *
     * @return array<string>
     */
    private function getExcludedStatements(ContainerBuilder $containerBuilder, array $excludedStatements): array
    {
        $excludedServiceStatements = [];
        foreach ($containerBuilder->findTaggedServiceIds('eniams.safe_migrations.statement') as $id => $tags) {
            $statement = new ($containerBuilder->getDefinition($id)->getClass());
            if (in_array($statement->getStatement(), $excludedStatements, true)) {
                $excludedServiceStatements[] = $statement->getStatement();
            }
        }

        return $excludedServiceStatements;
    }
}
