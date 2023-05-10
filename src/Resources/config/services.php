<?php

use Eniams\SafeMigrationsBundle\Listener\DoctrineMigrationDiffListener;
use Eniams\SafeMigrationsBundle\Statement\CreateIndexStatement;
use Eniams\SafeMigrationsBundle\Statement\DropStatement;
use Eniams\SafeMigrationsBundle\Statement\ModifyStatement;
use Eniams\SafeMigrationsBundle\Statement\NotNullStatement;
use Eniams\SafeMigrationsBundle\Statement\RenameStatement;
use Eniams\SafeMigrationsBundle\Statement\TruncateStatement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->private()

        ->set('eniams_safe_migrations.drop.statement', DropStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('eniams_safe_migrations.create_index.statement', CreateIndexStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('eniams_safe_migrations.not_null.statement', NotNullStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('eniams_safe_migrations.rename.statement', RenameStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('eniams_safe_migrations.truncate.statement', TruncateStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('eniams_safe_migrations.modify.statement', ModifyStatement::class)
        ->tag('eniams.safe_migrations.statement')

        ->set('safe_migrations.doctrine_migration_diff_listener', DoctrineMigrationDiffListener::class)
        ->tag('kernel.event_subscriber')
        ->args([
            service('eniams.safe_migrations.warning_factory'),
            service('eniams.safe_migrations.file_system'),
        ])
    ;
};
