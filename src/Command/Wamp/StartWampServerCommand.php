<?php

namespace App\Command\Wamp;

use App\WampServer\WampRouter;
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thruway\ClientSession;
use Thruway\Peer\Router;
use Thruway\Transport\PawlTransportProvider;
use Thruway\Transport\RatchetTransportProvider;

class StartWampServerCommand extends Command
{
    protected static $defaultName = "app:start";

    protected WampRouter $wampRouter;

    protected LoopInterface $loop;

    public function __construct()
    {
        parent::__construct();
        $this->loop = Loop::get();
    }

    /** @required */
    public function setWampRouter(WampRouter $wampRouter): self
    {
        $this->wampRouter = $wampRouter;

        return $this;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->wampRouter->start(false);
        $this->loop->run();

        return Command::SUCCESS;
    }
}
