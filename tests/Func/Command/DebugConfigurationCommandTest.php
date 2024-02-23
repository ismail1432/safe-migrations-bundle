<?php

namespace Eniams\SafeMigrationsBundle\Tests\Func\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DebugConfigurationCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testItOutputConfiguration(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $output = $this->getReadableOutput($commandTester);
        $this->assertStringContainsString('Statement that emits a warning', $output);
        $this->assertStringContainsString('Eniams\SafeMigrationsBundle\Tests\App\src\Statement\CustomStatement', $output);
        $this->assertStringContainsString('CUSTOM STATEMENT', $output);
        $this->assertStringContainsString('The migration contains a CUSTOM STATEMENT, double check the custom actions', $output);
        $this->assertStringContainsString('Excluded Statement', $output);
        $this->assertStringContainsString('MODIFY', $output);
        $this->assertStringContainsString('Critical Tables', $output);
        $this->assertStringContainsString('my_critical_table', $output);
    }

    private function getCommandTester(): CommandTester
    {
        $app = new Application(self::$kernel);

        return new CommandTester($app->find('eniams:debug-configuration'));
    }

    private function getReadableOutput(CommandTester $commandTester): string
    {
        return trim(preg_replace('/  +/', ' ',
            str_replace(PHP_EOL, '', $commandTester->getDisplay())
        ));
    }
}
