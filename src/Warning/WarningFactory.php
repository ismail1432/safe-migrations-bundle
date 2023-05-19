<?php

namespace Eniams\SafeMigrationsBundle\Warning;

use Eniams\SafeMigrationsBundle\Statement\StatementInterface;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class WarningFactory
{
    private WarningFormatter $warningFormatter;

    /**
     * @param StatementInterface[] $statements
     * @param string[]             $criticalTables
     * @param array<string>        $excludedStatements
     */
    public function __construct(private readonly iterable $statements, private readonly array $criticalTables = [], private readonly array $excludedStatements = [])
    {
        $this->warningFormatter = new WarningFormatter();
    }

    public function createWarning(string $migration): Warning
    {
        $warning = $this->createWarningBasedOnCriticalTable($migration);
        // Early return when critical changes on tables is found.
        // No need to check for critical statements.
        if ('' !== $warning->migrationWarning()) {
            return $warning;
        }

        return $this->createWarningBasedOnStatements($migration);
    }

    private function createWarningBasedOnStatements(string $migration): Warning
    {
        $commandOutputWarning = $migrationWarning = '';
        foreach ($this->statements as $statement) {
            if ($statement->supports($migration) && false === in_array($statement->getStatement(), $this->excludedStatements)) {
                $commandOutputWarning .= $this->warningFormatter->commandOutputWarning($statement->migrationWarning());
                $migrationWarning .= $this->warningFormatter->migrationWarningLine($statement->migrationWarning());
            }
        }

        return new Warning($commandOutputWarning, $migrationWarning);
    }

    private function createWarningBasedOnCriticalTable(string $migration): Warning
    {
        $commandOutputWarning = $migrationWarning = '';
        foreach ($this->criticalTables as $table) {
            if (str_contains($migration, $table)) {
                $commandOutputWarning = $this->warningFormatter->messageWhenCriticalTableHasChanges();
                $migrationWarning = $this->warningFormatter->migrationWarningWhenChangeOnCriticalTable($commandOutputWarning);

                break;
            }
        }

        return new Warning($commandOutputWarning, $migrationWarning);
    }
}
