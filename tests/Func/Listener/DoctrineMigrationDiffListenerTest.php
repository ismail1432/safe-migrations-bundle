<?php

namespace Eniams\SafeMigrationsBundle\Tests\Func\Listener;

use Eniams\SafeMigrationsBundle\Statement\ModifyStatement;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class DoctrineMigrationDiffListenerTest extends KernelTestCase
{
    private const BASE_DIR = __DIR__.'/../../App';
    private const MIGRATION_DIR = self::BASE_DIR.'/migrations';
    private const RESOURCES_DIR = self::BASE_DIR.'/resources';

    private const FILES = [
        'Version20230501WithAddColumnNotNull.php',
        'Version20230501WithRenameColumn.php',
        'Version20230501WithDrop.php',
        'Version20230501CustomStatement.php',
    ];

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function provideMigrationFiles(): iterable
    {
        yield 'With custom statement' => [
                'Version20230501CustomStatement.php',
                '// ⚠️ The migration contains a CUSTOM STATEMENT, double check the custom actions',
            ];
        yield 'With not null' => [
                'Version20230501WithAddColumnNotNull.php',
                "// ⚠️ The migration contains a NOT NULL statement, it's unsafe on heavy table and should be compliant with Zero downtime deployment",
            ];
        yield 'With drop' => [
                'Version20230501WithDrop.php',
                "// ⚠️ The migration contains a DROP statement, it's unsafe as you may loss data and should be compliant with Zero downtime deployment",
            ];
        yield 'With rename' => [
                'Version20230501WithRenameColumn.php',
                "// ⚠️ The migration contains a RENAME statement, it's unsafe as it should be compliant with Zero downtime deployment",
            ];
    }

    /**
     * @dataProvider provideMigrationFiles
     */
    public function testItAddCommentAndDisplayWarningInCommandOutput(string $filename, string $warning): void
    {
        $fileBaseDir = __DIR__.'/../../App';
        $this->moveToMigrationDir($filename);
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertStringContainsString($warning, $c = file_get_contents($fileBaseDir.'/migrations/'.$filename), sprintf('Warning not found in: %s', $c));

        $this->assertStringStartsWith(
            sprintf('Fake doctrine migrations diff command [WARNING] ⚠️ Dangerous operation detected in migration'),
            $this->getReadableOutput($commandTester)
        );
    }

    public function testItIgnoreExcludedOperations(): void
    {
        $fileBaseDir = __DIR__.'/../../App';
        $this->moveToMigrationDir($filename = 'Version20230501WithModify.php');
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertStringNotContainsString((new ModifyStatement())->migrationWarning(), $c = file_get_contents($fileBaseDir.'/migrations/'.$filename), sprintf('Warning not found in: %s', $c));

        $this->assertEquals('Fake doctrine migrations diff command', $this->getReadableOutput($commandTester));
    }

    public function testItWarningWhenMigrationContainsCriticalTable(): void
    {
        $fileBaseDir = __DIR__.'/../../App';
        $this->moveToMigrationDir($filename = 'Version20230501CriticalTable.php');
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $warning = 'The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe';

        $this->assertStringContainsString($warning, $c = file_get_contents($fileBaseDir.'/migrations/'.$filename), sprintf('Warning not found in: %s', $c));

        $this->assertStringStartsWith(
            sprintf('Fake doctrine migrations diff command [WARNING] ⚠️ Dangerous operation detected in migration'),
            $this->getReadableOutput($commandTester)
        );
    }

    private function moveToMigrationDir(string $filename): void
    {
        $source = file_get_contents(sprintf('%s/%s', self::RESOURCES_DIR, $filename));
        file_put_contents(self::MIGRATION_DIR.'/'.$filename, $source);
    }

    private function getCommandTester(): CommandTester
    {
        $app = new Application(self::$kernel);

        return new CommandTester($app->find('doctrine:migrations:diff'));
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $files = scandir(self::MIGRATION_DIR, \SCANDIR_SORT_DESCENDING);
        foreach ($files as $file) {
            if (str_ends_with($file, '.php')) {
                (new Filesystem())->remove(self::MIGRATION_DIR.'/'.$file);
            }
        }
    }

    private function getReadableOutput(CommandTester $commandTester): string
    {
        return trim(preg_replace('/  +/', ' ',
            str_replace(PHP_EOL, '', $commandTester->getDisplay())
        ));
    }
}
