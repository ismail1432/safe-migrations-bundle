<?php

declare(strict_types=1);

namespace Eniams\SafeMigrationsBundle\Tests\App\src\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(name: 'make:migration')]
class FakeMakeMigrationCommand extends Command
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
        parent::__construct();
    }

    // Fake make migration command to simulate the creations
    // of a new migration(s) file(s) when running the command.
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Fake make migration command');
        $event = new ConsoleTerminateEvent($this, $input, $output, 0);
        $this->dispatcher->dispatch($event, ConsoleEvents::TERMINATE);

        return 0;
    }
}
