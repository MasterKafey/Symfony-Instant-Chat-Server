<?php

namespace App\WampClient;

use Psr\Log\LoggerInterface;
use Thruway\AbstractSession;
use Thruway\Message\Message;
use Thruway\Role\AbstractRole;

class WampClientLoggerRole extends AbstractRole
{
    protected LoggerInterface $logger;

    /** @required */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function onMessage(AbstractSession $session, Message $msg): void
    {
        $this->logger->info('New request received');
    }

    public function handlesMessage(Message $msg): bool
    {
        return true;
    }
}
