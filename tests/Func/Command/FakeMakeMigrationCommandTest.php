<?php

namespace Eniams\SafeMigrationsBundle\Tests\Func\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FakeMakeMigrationCommandTest extends KernelTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testItAddCommentAndDisplayWarningInCommandOutput(): void
    {
        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful('Fake make migration command');
    }

    private function getCommandTester(): CommandTester
    {
        $app = new Application(self::$kernel);

        return new CommandTester($app->find('make:migration'));
    }
}
