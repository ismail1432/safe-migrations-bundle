<?php

namespace Eniams\SafeMigrationsBundle\Command;

use Eniams\SafeMigrationsBundle\Data\DebugCommandHeader;
use Eniams\SafeMigrationsBundle\Statement\StatementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DebugConfigurationCommand extends Command
{
    /**
     * @param StatementInterface[] $statements
     * @param string[]             $criticalTables
     * @param array<string>        $excludedStatements
     */
    public function __construct(private readonly iterable $statements, private readonly array $criticalTables = [], private readonly array $excludedStatements = [])
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('eniams:debug-configuration')
            ->setDescription('A command to debug the configuration of the Safe Migrations Bundle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createStatementDetails($output);
        $this->createExcludedStatements($output);
        $this->createCriticalTable($output);

        return Command::SUCCESS;
    }

    /* Statements and Message details
    +------------------------------------------------------------+----------------+
    | Class                                                      | Statement      |
    +------------------------------------------------------------+----------------+
    | Eniams\SafeMigrationsBundle\Statement\DropStatement        | DROP           |
    | Eniams\SafeMigrationsBundle\Statement\CreateIndexStatement | CREATE INDEX   |
    +------------------------------------------------------------+----------------+
    Message details
    +----------------+----------------------------------------------+
    | Statement      | Message                                      |
    +----------------+----------------------------------------------+
    | DROP           | The migration contains a DROP statement...   |
    +----------------+----------------------------------------------+
    */
    private function createStatementDetails(OutputInterface $output): void
    {
        $statementsRows = [];
        $statementsMessageRows = [];
        foreach ($this->statements as $statement) {
            if (false === in_array($statement->getStatement(), $this->excludedStatements)) {
                $statementsRows[] = [
                    get_class($statement),
                    $statement->getStatement(),
                ];

                $statementsMessageRows[] = [
                    $statement->getStatement(),
                    $statement->migrationWarning(),
                ];
            }
        }

        if ([] !== $statementsRows) {
            $statementTable = new Table($output);
            $statementTable
                ->setHeaders([
                    DebugCommandHeader::Fqcn->value,
                    DebugCommandHeader::Statement->value,
                ])
            ;

            $statementMessageTable = new Table($output);
            $statementMessageTable
                ->setHeaders([
                    DebugCommandHeader::Statement->value,
                    DebugCommandHeader::Message->value,
                ])
            ;

            $statementTable->setRows($statementsRows);
            $statementMessageTable->setRows($statementsMessageRows);

            $output->writeln('<fg=green>Statement that emits a warning</>');
            $statementTable->render();

            $output->writeln('<fg=green>Message details</>');
            $statementMessageTable->render();

            return;
        }

        $output->writeln('<fg=green>No statement configured</>');
    }

    /*
    +--------------------+
    | Excluded Statement |
    +--------------------+
    | TRUNCATE TABLE     |
    +--------------------+
    */
    private function createExcludedStatements(OutputInterface $output): void
    {
        $excludedStatementsRows = [];
        foreach ($this->excludedStatements as $excludedStatement) {
            $excludedStatementsRows[] = [
                $excludedStatement,
            ];
        }

        if ([] !== $excludedStatementsRows) {
            $excludedStatementTable = new Table($output);
            $excludedStatementTable
                ->setHeaders([
                    'Excluded Statement',
                ])
            ;
            $excludedStatementTable->setRows($excludedStatementsRows);
            $excludedStatementTable->render();

            return;
        }
        $output->writeln('<fg=green>No statement excluded</>');
    }

    /*
     +-----------------+
     | Critical Tables |
     +-----------------+
     | user            |
     +-----------------+
     */
    private function createCriticalTable(OutputInterface $output): void
    {
        $criticalTablesRows = [];
        foreach ($this->criticalTables as $criticalTable) {
            $criticalTablesRows[] = [
                $criticalTable,
            ];
        }
        if ([] !== $criticalTablesRows) {
            $criticalsTableTable = new Table($output);
            $criticalsTableTable
                ->setHeaders([
                    'Critical Tables',
                ])
            ;
            $criticalsTableTable->setRows($criticalTablesRows);
            $criticalsTableTable->render();

            return;
        }
        $output->writeln('<fg=green>No critical tables configured</>');
    }
}
