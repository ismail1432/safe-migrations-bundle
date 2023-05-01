<?php

namespace Eniams\SafeMigrationsBundle\Statement;

interface StatementInterface
{
    public function getStatement(): string;

    public function supports(string $migration): bool;

    public function migrationWarning(): string;
}
