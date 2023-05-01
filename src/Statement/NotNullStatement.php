<?php

namespace Eniams\SafeMigrationsBundle\Statement;

final class NotNullStatement extends AbstractStatement
{
    private const STATEMENT = 'NOT NULL';
    protected string $migrationWarning = "The migration contains a NOT NULL statement, it's unsafe on heavy table and should be compliant with Zero downtime deployment";

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
