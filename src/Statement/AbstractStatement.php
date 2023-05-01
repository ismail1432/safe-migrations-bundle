<?php

namespace Eniams\SafeMigrationsBundle\Statement;

abstract class AbstractStatement implements StatementInterface
{
    protected string $migrationWarning;

    public function __construct()
    {
        $this->migrationWarning = $this->migrationWarning ?? sprintf("The migration contains a %s statement, it's unsafe as it should be compliant with Zero downtime deployment", $this->getStatement());
    }

    public function migrationWarning(): string
    {
        return $this->migrationWarning;
    }

    public function supports(string $migration): bool
    {
        // Avoid to search statement in migration comment
        // @TODO improve this.
        $migration = str_replace('// this up() migration is auto-generated, please modify it to your needs', '', $migration);

        return str_contains(strtoupper($migration), $this->getStatement());
    }

    abstract public function getStatement(): string;
}
