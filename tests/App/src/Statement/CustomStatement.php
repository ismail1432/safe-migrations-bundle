<?php

namespace Eniams\SafeMigrationsBundle\Tests\App\src\Statement;

use Eniams\SafeMigrationsBundle\Statement\AbstractStatement;

final class CustomStatement extends AbstractStatement
{
    private const STATEMENT = 'CUSTOM STATEMENT';
    protected string $migrationWarning = 'The migration contains a CUSTOM STATEMENT, double check the custom actions';

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
