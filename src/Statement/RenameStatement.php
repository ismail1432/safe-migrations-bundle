<?php

namespace Eniams\SafeMigrationsBundle\Statement;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class RenameStatement extends AbstractStatement
{
    private const STATEMENT = 'RENAME';
    protected string $migrationWarning;

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
