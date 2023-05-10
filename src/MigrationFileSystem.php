<?php

namespace Eniams\SafeMigrationsBundle;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 *
 * @author Smaïne Milianni <smaine.milianni@gmail.com>
 */
final class MigrationFileSystem
{
    private ?string $newestMigrationFileName;

    public function __construct(private readonly string $doctrineMigrationsDir)
    {
    }

    public function newestMigrationFileName(): ?string
    {
        $finder = new Finder();
        $finder->in($this->doctrineMigrationsDir)->files()->name('*.php')->sortByName()->reverseSorting();

        if (false === $finder->hasResults()) {
            return null;
        }

        /** @var SplFileInfo $lastFile */
        $lastFile = $finder->getIterator()->current();

        return $this->newestMigrationFileName = $lastFile->getFilename();
    }

    public function newestFilePath(): string
    {
        if (null === $this->newestMigrationFileName) {
            throw new \LogicException('newestMigrationFileName should be defined at this stage.');
        }

        return sprintf('%s/%s', $this->doctrineMigrationsDir, $this->newestMigrationFileName);
    }

    public function migrationName(): string
    {
        if (null === $this->newestMigrationFileName) {
            throw new \LogicException('newestMigrationFileName should be defined at this stage.');
        }

        return str_replace('.php', '', $this->newestMigrationFileName);
    }

    public function extractMigration(string $migrationContent): string
    {
        $upStart = strpos($migrationContent, 'function up');
        $upEnd = strpos($migrationContent, 'function down');

        if (!is_int($upStart) || 0 === $upStart || $upEnd <= $upStart) {
            throw new \LogicException('no function up or function down were found the migration file');
        }

        return substr($migrationContent, $upStart, $upEnd - $upStart);
    }
}
