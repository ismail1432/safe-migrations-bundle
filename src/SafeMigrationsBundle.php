<?php

namespace Eniams\SafeMigrationsBundle;

use Eniams\SafeMigrationsBundle\Listener\DoctrineMigrationDiffListener;
use Eniams\SafeMigrationsBundle\Statement\StatementInterface;
use Eniams\SafeMigrationsBundle\Warning\WarningFactory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SafeMigrationsBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        /* @phpstan-ignore-next-line */
        $definition
            ->rootNode()
                ->children()
                    ->arrayNode('critical_tables')->canBeUnset()
                        ->scalarPrototype()->end()
                    ->end()
                    ->arrayNode('excluded_statements')->canBeUnset()
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('migrations_path')->isRequired()->end()
                ->end()
            ->end()
        ;
    }

    /** @phpstan-ignore-next-line */
    public function loadExtension(array $configs, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/Resources/config'));
        $loader->load('services.php');

        $migrationsPath = $configs['migrations_path'];
        $criticalTables = $configs['critical_tables'];
        $excludedStatements = $configs['excluded_statements'];

        if (!is_dir($migrationsPath)) {
            throw new \InvalidArgumentException(sprintf('You must provide a valid path to your migrations directory. "%s" given.', $migrationsPath));
        }

        $containerBuilder->setDefinition('eniams.safe_migrations.file_system', new Definition(MigrationFileSystem::class))
            ->setArgument(0, $migrationsPath)
        ;

        $containerBuilder->registerForAutoconfiguration(StatementInterface::class)->addTag('eniams.safe_migrations.statement');
        $excludedServiceStatements = $this->getExcludedStatements($containerBuilder, $excludedStatements);

        $containerBuilder->setDefinition('eniams.safe_migrations.warning_factory', new Definition(WarningFactory::class))
            ->setArguments([tagged_iterator('eniams.safe_migrations.statement', exclude: $excludedServiceStatements),
                $criticalTables]
            )
        ;

        $containerConfigurator
            ->services()
            ->set('safe_migrations.doctrine_migration_diff_listener', DoctrineMigrationDiffListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                new Reference('eniams.safe_migrations.warning_factory'),
                service('eniams.safe_migrations.file_system'),
            ])
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
                $excludedServiceStatements[] = $id;
            }
        }

        return $excludedServiceStatements;
    }
}
