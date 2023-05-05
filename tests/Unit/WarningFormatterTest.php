<?php

namespace Eniams\SafeMigrationsBundle\tests\Unit;

use Eniams\SafeMigrationsBundle\Warning\Warning;
use Eniams\SafeMigrationsBundle\Warning\WarningFormatter;
use PHPUnit\Framework\TestCase;

final class WarningFormatterTest extends TestCase
{
    public function testWarningFormatter(): void
    {
        $warning = new Warning('command output warning', 'migration warning');
        $formatter = new WarningFormatter();

        $this->assertEquals(
            "        // ⚠️ migration warning\n",
            $formatter->migrationWarningLine($warning->migrationWarning())
        );
        $this->assertEquals(
            " command output warning \n",
            $formatter->commandOutputWarning($warning->commandOutputWarning())
        );

        $this->assertEquals(
            "️The migration contains change(s) on a critical table(s) that can cause downtime, double check that changes are safe. \n",
            $formatter->messageWhenCriticalTableHasChanges()
        );

        $this->assertEquals(
            '⚠️  Dangerous operation detected in migration "migrationName"!',
            $formatter->dangerousOperationMessage('migrationName')
        );
    }
}
