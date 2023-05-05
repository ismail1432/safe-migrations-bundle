<?php

namespace Eniams\SafeMigrationsBundle\tests\Unit;

use Eniams\SafeMigrationsBundle\Statement\NotNullStatement;
use Eniams\SafeMigrationsBundle\Warning\WarningFactory;
use PHPUnit\Framework\TestCase;

final class WarningFactoryTest extends TestCase
{
    public function testWarningFactoryCreateFromCriticalTable(): void
    {
        $factory = new WarningFactory(
            [new NotNullStatement()],
            ['user', 'product']
        );

        $warning = $factory->createWarning('ALTER TABLE user ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->assertEquals(
            "        // ⚠️ ️The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe. \n",
            $warning->migrationWarning()
        );
        $this->assertEquals(
            "️The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe. \n",
            $warning->commandOutputWarning()
        );
    }

    public function testWarningFactoryCreateFromStatement(): void
    {
        $factory = new WarningFactory(
            [new NotNullStatement()],
            ['user', 'product']
        );

        $warning = $factory->createWarning('ALTER TABLE city ADD COLUMN name VARCHAR(255) NOT NULL');
        $this->assertEquals(
            "        // ⚠️ The migration contains a NOT NULL statement, it's unsafe on heavy table and should be compliant with Zero downtime deployment\n",
            $warning->migrationWarning()
        );
        $this->assertEquals(
            trim("The migration contains a NOT NULL statement, it's unsafe on heavy table and should be compliant with Zero downtime deployment"),
            trim($warning->commandOutputWarning())
        );
    }

    public function testWarningIsEmptyWhenNoCriticalChangeAreFound(): void
    {
        $factory = new WarningFactory(
            [new NotNullStatement()],
            ['user', 'product']
        );

        $warning = $factory->createWarning('ALTER TABLE city ADD COLUMN name VARCHAR(255) DEFAULT NULL');
        $this->assertEquals('', $warning->migrationWarning());
        $this->assertEquals('', $warning->commandOutputWarning());
    }
}
