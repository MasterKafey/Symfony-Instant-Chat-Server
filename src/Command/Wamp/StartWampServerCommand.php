<?php

namespace App\Command\Wamp;

use App\WampServer\WampRouter;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thruway\ClientSession;
use Thruway\Peer\Router;
use Thruway\Transport\PawlTransportProvider;
use Thruway\Transport\RatchetTransportProvider;

#[AsCommand('app:start')]
class StartWampServerCommand extends Command
{
    public function configure(): void
    {
        $this->addArgument('server-url', InputArgument::REQUIRED, 'Url of the websocket server');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $wampRouter = new WampRouter($input->getArgument('server-url'));
        $wampRouter->start(false);
        Loop::get()->run();

        return Command::SUCCESS;
    }
}
