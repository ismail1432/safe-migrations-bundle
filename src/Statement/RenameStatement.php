<?php

namespace Eniams\SafeMigrationsBundle\Statement;

final class RenameStatement extends AbstractStatement
{
    private const STATEMENT = 'RENAME';
    protected string $migrationWarning;

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
