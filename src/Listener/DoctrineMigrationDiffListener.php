<?php

namespace Eniams\SafeMigrationsBundle\Listener;

use Eniams\SafeMigrationsBundle\MigrationFileSystem;
use Eniams\SafeMigrationsBundle\Statement\StatementInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DoctrineMigrationDiffListener implements EventSubscriberInterface
{
    private const UP_LINE = 'public function up(Schema $schema): void';

    /**
     * @param iterable<StatementInterface> $statements
     * @param array<string>                $criticalTables     table that can cause downtime
     * @param array<string>                $excludedOperations operation to exclude from warning
     */
    public function __construct(
        private readonly iterable $statements,
        private readonly array $criticalTables,
        private readonly array $excludedOperations,
        private readonly MigrationFileSystem $fileSytem
    ) {
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if (null !== $event->getCommand() && !$this->supports($event)) {
            return;
        }

        $io = new SymfonyStyle($event->getInput(), $event->getOutput());

        if (null === $this->fileSytem->newestMigrationFileName()) {
            $io->info('No migration file found, skipping seeking unsafe operations...');

            return;
        }

        $newestMigrationFile = $this->fileSytem->newestFilePath();

        if (false === $f = fopen($newestMigrationFile, 'rb+')) {
            throw new \RuntimeException(sprintf('Unable to open file %s', $newestMigrationFile));
        }

        $migrationFileContent = file_get_contents($newestMigrationFile);
        if (false === $migrationFileContent) {
            throw new \RuntimeException(sprintf('Unable to read file %s', $newestMigrationFile));
        }

        $migrationWarning = '';
        $commandOutputWarning = '';

        $migration = $this->fileSytem->extractMigration($migrationFileContent);
        foreach ($this->criticalTables as $table) {
            if (str_contains($migration, $table)) {
                $changesOnCriticalTable = $this->messageWhenCriticalTableHasChanges();
                $migrationWarning .= $this->migrationWarningWhenChangeOnCriticalTable($changesOnCriticalTable);
                $commandOutputWarning .= $changesOnCriticalTable;
                break;
            }
        }

        // If no critical changes on tables is found, check for critical statement.
        if ('' === $migrationWarning) {
            foreach ($this->statements as $statement) {
                if ($statement->supports($migration) && !in_array($statement->getStatement(), $this->excludedOperations, true)) {
                    $commandOutputWarning .= $this->commandOutputWarning($statement->migrationWarning());
                    $migrationWarning .= $this->migrationWarningLine($statement->migrationWarning());
                }
            }
        }

        // No critical statement found, exit.
        if ('' === $migrationWarning) {
            return;
        }

        while (false !== $buffer = fgets($f)) {
            if (str_contains($buffer, self::UP_LINE)) {
                $position = ftell($f);
                $zddMigrationWarningComment = substr_replace($migrationFileContent, $migrationWarning, $position + 6, 0);
                file_put_contents($newestMigrationFile, $zddMigrationWarningComment);
                break;
            }
        }
        fclose($f);

        $migrationName = $this->fileSytem->migrationName();
        $io->warning($this->dangerousOperationMessage($migrationName));
        $io->warning($commandOutputWarning);
    }

    private function supports(ConsoleTerminateEvent $event): bool
    {
        return null !== $event->getCommand() && 'doctrine:migrations:diff' === $event->getCommand()->getName() && 0 === $event->getExitCode();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ];
    }

    private function commandOutputWarning(string $statementCommandOutputWarning): string
    {
        return " $statementCommandOutputWarning \n";
    }

    private function migrationWarningLine(string $statementWarning): string
    {
        return "        // ⚠️ ".$statementWarning."\n";
    }

    private function messageWhenCriticalTableHasChanges(): string
    {
        return "️The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe. \n";
    }

    private function dangerousOperationMessage(string $migrationName): string
    {
        return sprintf('⚠️  Dangerous operation detected in migration "%s"!', $migrationName);
    }

    private function migrationWarningWhenChangeOnCriticalTable(string $changesOnCriticalTable): string
    {
        return '        // ⚠️ '.$changesOnCriticalTable;
    }
}
