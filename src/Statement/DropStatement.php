<?php

namespace Eniams\SafeMigrationsBundle\Statement;

final class DropStatement extends AbstractStatement
{
    private const STATEMENT = 'DROP';
    protected string $migrationWarning = "The migration contains a DROP statement, it's unsafe as you may loss data and should be compliant with Zero downtime deployment";

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
