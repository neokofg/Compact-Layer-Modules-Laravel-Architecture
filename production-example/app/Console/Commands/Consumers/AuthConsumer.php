<?php

namespace App\Console\Commands\Consumers;

use App\Services\MailService;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\MessageConsumer;
use Junges\Kafka\Facades\Kafka;
use Illuminate\Console\Command;

class AuthConsumer extends Command
{
    protected $signature = 'consume:auth';

    protected $description = 'Отправляет сообщения';

    public function handle()
    {
        $consumer = Kafka::consumer(['auth'])
            ->withBrokers('172.17.0.1:9092')
            ->withAutoCommit()
            ->withHandler(function (ConsumerMessage $message, MessageConsumer $consumer) {
                $mailService = new MailService();
                $message = $message->getBody();
                $mailService->userCodeSend($message['code'], $message['email']);
            })
            ->build();

        $consumer->consume();
    }
}
