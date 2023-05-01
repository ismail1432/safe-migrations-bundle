<?php

namespace Eniams\SafeMigrationsBundle\Statement;

final class ModifyStatement extends AbstractStatement
{
    private const STATEMENT = 'MODIFY';

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
