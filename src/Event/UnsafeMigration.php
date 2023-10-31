<?php

namespace Eniams\SafeMigrationsBundle\Event;

/**
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class UnsafeMigration
{
    public function __construct(private readonly string $migrationName, private readonly string $migrationFileContent, private readonly string $migrationFileContentWithWarning)
    {
    }

    public function getMigrationFileContent(): string
    {
        return $this->migrationFileContent;
    }

    public function getMigrationName(): string
    {
        return $this->migrationName;
    }

    public function getMigrationFileContentWithWarning(): string
    {
        return $this->migrationFileContentWithWarning;
    }
}
