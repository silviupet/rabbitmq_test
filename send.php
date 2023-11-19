<?php


require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->confirm_select();
$channel->set_ack_handler(
    function (AMQPMessage $message) {
        echo "Message acked with content " . $message->body . PHP_EOL;
    }
);
$channel->set_nack_handler(
    function (AMQPMessage $message) {
        echo "Message nacked with content " . $message->body . PHP_EOL;
    }
);



$channel->queue_declare('helloSilviu', false, true, false, false);
$channel->exchange_declare("someExchange", 'fanout', false, false, true);

$msg = new AMQPMessage('Hello World Silviu!',
array('delivery_mode' => 2));
$channel->basic_publish($msg, '', 'helloSilviu');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();

