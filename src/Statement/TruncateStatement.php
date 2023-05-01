<?php

namespace Eniams\SafeMigrationsBundle\Statement;

final class TruncateStatement extends AbstractStatement
{
    private const STATEMENT = 'TRUNCATE TABLE';
    protected string $migrationWarning = "The migration contains a TRUNCATE statement, it's unsafe as you may loss data and should be compliant with Zero downtime deployment";

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
