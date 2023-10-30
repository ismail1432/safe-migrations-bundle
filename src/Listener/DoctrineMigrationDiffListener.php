<?php

namespace Eniams\SafeMigrationsBundle\Listener;

use Eniams\SafeMigrationsBundle\Event\UnsafeMigration;
use Eniams\SafeMigrationsBundle\Event\UnsafeMigrationEvent;
use Eniams\SafeMigrationsBundle\MigrationFileSystem;
use Eniams\SafeMigrationsBundle\Warning\WarningFactory;
use Eniams\SafeMigrationsBundle\Warning\WarningFormatter;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class DoctrineMigrationDiffListener implements EventSubscriberInterface
{
    private const UP_LINE = 'public function up(Schema $schema)';
    private const MIGRATION_COMMANDS = [
        'doctrine:migrations:diff',
        'make:migration',
    ];
    private WarningFormatter $warningFormatter;

    public function __construct(
        private readonly WarningFactory $warningFactory,
        private readonly MigrationFileSystem $fileSystem,
        private readonly EventDispatcherInterface $dispatcher
    ) {
        $this->warningFormatter = new WarningFormatter();
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if (null !== $event->getCommand() && !$this->supports($event)) {
            return;
        }

        $io = new SymfonyStyle($event->getInput(), $event->getOutput());

        if (null === $this->fileSystem->newestMigrationFileName()) {
            $io->info('No migration file found, skipping seeking unsafe operations...');

            return;
        }

        $newestMigrationFile = $this->fileSystem->newestFilePath();

        if (false === $f = fopen($newestMigrationFile, 'rb+')) {
            throw new \RuntimeException(sprintf('Unable to open file %s', $newestMigrationFile));
        }

        $migrationFileContent = file_get_contents($newestMigrationFile);
        if (false === $migrationFileContent) {
            throw new \RuntimeException(sprintf('Unable to read file %s', $newestMigrationFile));
        }

        $migration = $this->fileSystem->extractMigration($migrationFileContent);

        $warning = $this->warningFactory->createWarning($migration);

        // No critical changes found, exit.
        if ('' === $migrationWarning = $warning->migrationWarning()) {
            return;
        }

        $migrationFileContentWithWarning = '';
        while (false !== $buffer = fgets($f)) {
            if (str_contains($buffer, self::UP_LINE)) {
                $position = ftell($f);
                $migrationFileContentWithWarning = substr_replace($migrationFileContent, $migrationWarning, $position + 6, 0);
                file_put_contents($newestMigrationFile, $migrationFileContentWithWarning);
                break;
            }
        }
        fclose($f);
        $migrationName = $this->fileSystem->migrationName();

        if ('' !== $migrationFileContentWithWarning) {
            $this->dispatcher->dispatch(new UnsafeMigrationEvent(new UnsafeMigration($migrationName, $migrationFileContent, $migrationFileContentWithWarning)), UnsafeMigrationEvent::class);
        }

        $io->warning($this->warningFormatter->dangerousOperationMessage($migrationName));
        $io->warning($warning->commandOutputWarning());
    }

    private function supports(ConsoleTerminateEvent $event): bool
    {
        return null !== $event->getCommand() && in_array($event->getCommand()->getName(), self::MIGRATION_COMMANDS) && 0 === $event->getExitCode();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ];
    }
}
