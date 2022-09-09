<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;


/*
 * A service class, responsible for handing rabbitmq operations.
 */

class RabbitMQService
{
    private ?AMQPStreamConnection $connection = null;
    public ?AMQPChannel $channel = null;
    private AMQPMessage $message;

    public function __construct() {
        // create a connection & a RabbitMQ channel
        $this->createConnection();
        $this->defineChanel();
    }

    // Create and store RabbitMQ connection
    public function createConnection()
    {
        // Create connection if not exists
        if (!$this->connection) {
            $this->connection = new AMQPStreamConnection(RMQ_HOST, RMQ_PORT, RMQ_USER, RMQ_PASSWORD, RMQ_VHOST);
        }
    }

    // Creates a channel
    public function defineChanel(bool $createNewChanel = false): AMQPChannel
    {
        // Return new channel if asked explicitly
        if ($createNewChanel) {
            return $this->connection->channel();
        }

        // Return channel if already exists
        if ($this->channel) {
            return $this->channel;
        }

        // Create a new channel & return
        $this->channel = $this->connection->channel();
        return $this->channel;
    }

    // Declare sms exchange, if it does not exist
    public function declareExchange(string $exchangeName = SMS_EXCHANGE)
    {
        $this->channel->exchange_declare($exchangeName, SMS_EXCHANGE_TYPE, false, true, false);
    }

    // Declare sms queue if does not exists
    public function declareQueue(string $queueName = SMS_QUEUE): array
    {
        return $this->channel->queue_declare($queueName, false, true, false, false);
    }

    // Binds routing key
    public function queueBind(string $routingKey = SMS_QUEUE_ROUTING_KEY)
    {
        $this->channel->queue_bind(SMS_QUEUE, SMS_EXCHANGE, $routingKey);
    }

    // Converts our payload to rabbitmq compatible
    public function prepareMessage(array $message)
    {
        $this->message = new AMQPMessage(
            json_encode($message),
            AMQPMessage::DELIVERY_MODE_PERSISTENT
        );
    }

    // Produce a messages to the queue
    public function publishTheMessage()
    {
        $this->channel->basic_publish($this->message, SMS_EXCHANGE, SMS_QUEUE_ROUTING_KEY);
    }

    // Close to release the resources
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
