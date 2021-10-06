<?php

namespace App\Command\Wamp;


use App\Amqp\Message\Event\MessageSendEvent;
use App\Entity\User;
use App\WampServer\Publisher\MessagePublisher;
use Doctrine\ORM\EntityManagerInterface;
use React\EventLoop\Loop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'app:test';

    protected MessagePublisher $messagePublisher;

    protected EntityManagerInterface $entityManager;

    /** @required */
    public function setMessagePublisher(MessagePublisher $messagePublisher): self
    {
        $this->messagePublisher = $messagePublisher;

        return $this;
    }

    /** @required */
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    public function configure()
    {
        $this->addArgument('message', InputArgument::REQUIRED, 'Message');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $messageSendEvent = new MessageSendEvent();
        $messageSendEvent
            ->setContent($input->getArgument('message'))
            ->setUser($this->entityManager->getRepository(User::class)->findOneBy([]));;
        $this->messagePublisher->publishMessageSend($messageSendEvent);

        return Command::SUCCESS;
    }
}
