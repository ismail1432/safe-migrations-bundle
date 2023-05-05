<?php

namespace Eniams\SafeMigrationsBundle\Warning;

final class Warning
{
    private string $commandOutputWarning;
    private string $migrationWarning;

    public function __construct(string $commandOutputWarning, string $migrationWarning)
    {
        $this->commandOutputWarning = $commandOutputWarning;
        $this->migrationWarning = $migrationWarning;
    }

    public function commandOutputWarning(): string
    {
        return $this->commandOutputWarning;
    }

    public function migrationWarning(): string
    {
        return $this->migrationWarning;
    }
}
