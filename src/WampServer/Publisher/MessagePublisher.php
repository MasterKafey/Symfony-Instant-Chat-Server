<?php

namespace App\WampServer\Publisher;


use App\Amqp\Message\Event\MessageSendEvent;
use App\WampServer\WampRouter;
use App\WampServer\WampTopic;
use React\Promise\PromiseInterface;

class MessagePublisher extends AbstractPublisher
{
    public function publishMessageSend(MessageSendEvent $messageSendEvent): PromiseInterface
    {
        $payload = [
            'username' => $messageSendEvent->getUser()->getUsername(),
            'first_name' => $messageSendEvent->getUser()->getFirstName(),
            'last_name' => $messageSendEvent->getUser()->getLastName(),
            'content' => $messageSendEvent->getContent(),
        ];

        return $this->publish(
            [WampRouter::REALM_USER],
            WampTopic::MESSAGE_SEND,
            $payload,
        );
    }
}
