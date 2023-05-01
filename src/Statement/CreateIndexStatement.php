<?php

namespace Eniams\SafeMigrationsBundle\Statement;

class CreateIndexStatement extends AbstractStatement
{
    private const STATEMENT = 'CREATE INDEX';
    protected string $migrationWarning = "The migration contains a CREATE INDEX statement, it's unsafe on heavy table did you add the CONCURRENTLY option?";

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
