<?php

namespace Eniams\SafeMigrationsBundle\tests\Func\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class FakeDoctrineMigrationsDiffCommandTest extends KernelTestCase
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

        $commandTester->assertCommandIsSuccessful('Fake doctrine migrations diff command');
    }

    private function getCommandTester(): CommandTester
    {
        $app = new Application(self::$kernel);

        return new CommandTester($app->find('doctrine:migrations:diff'));
    }
}
