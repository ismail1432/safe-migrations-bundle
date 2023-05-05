<?php

namespace Eniams\SafeMigrationsBundle\Statement;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class ModifyStatement extends AbstractStatement
{
    private const STATEMENT = 'MODIFY';

    public function getStatement(): string
    {
        return self::STATEMENT;
    }
}
