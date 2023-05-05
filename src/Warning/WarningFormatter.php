<?php

namespace Eniams\SafeMigrationsBundle\Warning;

/**
 * Format warning messages.
 *
 * @internal
 */
final class WarningFormatter
{
    public function commandOutputWarning(string $statementCommandOutputWarning): string
    {
        return " $statementCommandOutputWarning \n";
    }

    public function migrationWarningLine(string $statementWarning): string
    {
        return '        // ⚠️ '.$statementWarning."\n";
    }

    public function messageWhenCriticalTableHasChanges(): string
    {
        return "️The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe. \n";
    }

    public function dangerousOperationMessage(string $migrationName): string
    {
        return sprintf('⚠️  Dangerous operation detected in migration "%s"!', $migrationName);
    }

    public function migrationWarningWhenChangeOnCriticalTable(string $changesOnCriticalTable): string
    {
        return '        // ⚠️ '.$changesOnCriticalTable;
    }
}
