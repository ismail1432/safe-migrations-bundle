<?php

namespace Eniams\SafeMigrationsBundle\Statement;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
interface StatementInterface
{
    public function getStatement(): string;

    public function supports(string $migration): bool;

    public function migrationWarning(): string;
}
