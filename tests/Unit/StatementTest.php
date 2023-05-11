<?php

namespace Eniams\SafeMigrationsBundle\Tests\Unit;

use Eniams\SafeMigrationsBundle\Statement\AbstractStatement;
use Eniams\SafeMigrationsBundle\Statement\CreateIndexStatement;
use Eniams\SafeMigrationsBundle\Statement\DropStatement;
use Eniams\SafeMigrationsBundle\Statement\ModifyStatement;
use Eniams\SafeMigrationsBundle\Statement\NotNullStatement;
use Eniams\SafeMigrationsBundle\Statement\RenameStatement;
use Eniams\SafeMigrationsBundle\Statement\TruncateStatement;
use PHPUnit\Framework\TestCase;

class StatementTest extends TestCase
{
    public function provideStatements(): iterable
    {
        yield 'Drop' => [new DropStatement(), 'DROP', "The migration contains a DROP statement, it's unsafe as you may loss data and should be compliant with Zero downtime deployment"];
        yield 'NotNull' => [new NotNullStatement(), 'NOT NULL', "The migration contains a NOT NULL statement, it's unsafe on heavy table and should be compliant with Zero downtime deployment"];
        yield 'Rename' => [new RenameStatement(), 'RENAME', "The migration contains a RENAME statement, it's unsafe as it should be compliant with Zero downtime deployment"];
        yield 'Truncate' => [new TruncateStatement(), 'TRUNCATE TABLE', "The migration contains a TRUNCATE statement, it's unsafe as you may loss data and should be compliant with Zero downtime deployment"];
        yield 'Create index' => [new CreateIndexStatement(), 'CREATE INDEX', "The migration contains a CREATE INDEX statement, it's unsafe on heavy table did you add the CONCURRENTLY option?"];
        yield 'Modify' => [new ModifyStatement(), 'MODIFY', "The migration contains a MODIFY statement, it's unsafe as it should be compliant with Zero downtime deployment"];
    }

    /**
     * @dataProvider provideStatements
     */
    public function testItReturnsTheStatement(AbstractStatement $statement, string $stringStatement, string $warning): void
    {
        $this->assertSame($stringStatement, $statement->getStatement());
    }

    public function provideSupports(): iterable
    {
        yield 'Drop' => [new DropStatement(), 'DROP TABLE table_name)'];
        yield 'NotNull' => [new NotNullStatement(), 'ALTER TABLE BAR ADD COLUMN FOO VARCHAR(255) NOT NULL'];
        yield 'Rename' => [new RenameStatement(), 'ALTER TABLE FOO RENAME COLUMN BAR TO FOOBAR'];
        yield 'Truncate' => [new TruncateStatement(), 'TRUNCATE TABLE FOO'];
        yield 'Modify' => [new ModifyStatement(), 'ALTER TABLE table_name MODIFY COLUMN column_name datatype'];
        yield 'Create index' => [new CreateIndexStatement(), 'CREATE INDEX foo_idx ON foo (bar)'];
    }

    /**
     * @dataProvider provideSupports
     */
    public function testSupports(AbstractStatement $statement, string $instruction): void
    {
        $this->assertTrue($statement->supports($instruction));
    }

    public function provideUnSupports(): iterable
    {
        yield 'Drop' => [new DropStatement(), 'CREATE INDEX foo_idx ON foo (bar)'];
        yield 'NotNull' => [new NotNullStatement(), 'DROP TABLE table_name'];
        yield 'Rename' => [new RenameStatement(), 'ALTER TABLE BAR ADD COLUMN FOO VARCHAR(255) NOT NULL'];
        yield 'Truncate' => [new TruncateStatement(), 'ALTER TABLE FOO RENAME COLUMN BAR TO FOOBAR'];
        yield 'Modify' => [new ModifyStatement(), 'TRUNCATE TABLE FOO'];
        yield 'Create index' => [new CreateIndexStatement(), 'ALTER TABLE table_name MODIFY COLUMN column_name datatype'];
    }

    /**
     * @dataProvider provideUnSupports
     */
    public function testItUnSupports(AbstractStatement $statement, string $instruction): void
    {
        $this->assertFalse($statement->supports($instruction));
    }
}
