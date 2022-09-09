<?php

namespace App\Controllers;

use App\Helpers\Validator;
use App\Services\RabbitMQService;
use PhpAmqpLib\Message\AMQPMessage;
use App\Helpers\Response;


/**
 * QueueController
 * Responsible to produce, consumes and show totals.
 */
class QueueController 
{
    // Holds page name this controller called from.
    private string $pageName = '';

    /**
     * Create and set RabbitMQ connection
     * And call respective method based on request-method type
     */
    public function __construct(string $pageName = '')
    {
        // Store page name called from
        $this->pageName = $pageName;

        // Detect user request type. And call respective method to handle it.
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->produceMessage(new RabbitMQService());
                break;

            case 'GET':
                if ($this->pageName == 'queues_single.php') {
                    $this->consumeSingleMessage(new RabbitMQService());
                    return;
                }

                if ($this->pageName == 'queues_total.php') {
                    $this->queueSize(new RabbitMQService());
                    return;
                }

                $this->consumeAllMessages(new RabbitMQService());
        }
    }


    // Send messages to the queue
    public function produceMessage(RabbitMQService $service): void
    {
        // Accepted input fields
        $fields = ['to','message'];

        // Validate input fields
        $data = Validator::validate($fields);

        // Asking server, we want message publish acknowledgement.
        $service->channel->confirm_select();

        // Declare sms exchange, if it does not exist
        $service->declareExchange();

        // Declare sms queue if does not exists
        $service->declareQueue();

        // Bind sms queue
        $service->queueBind();

        // Converts user input array to AMQPMessage
        $service->prepareMessage($data);

        // Publish the message
        $service->publishTheMessage();

        // Wait upto 5 seconds for acknowledgment
        $service->channel->wait_for_pending_acks(5);

        // Response a success message to the user
        Response::send(
            [
                'success' => true,
                'message' => 'Message has been sent',
                'data' => $data
            ],
            200
        );

        // Close channel & connection
        $service->close();
    }

    // Fetch all messages from the queue
    public function consumeAllMessages(RabbitMQService $service)
    {
        $messages = [];
        $messageHandler = function (AMQPMessage $message) use(&$messages) {
            /**
             * It's not good a practice storing all messages this way.
             * It's ok, Since it's only assessment requirement to show all messages at once by a single endpoint call request.
             */
            $messages[] = json_decode($message->getBody(), true);
        };
        $service->declareExchange();
        $queue = $service->declareQueue();
        $service->channel->basic_consume(SMS_QUEUE, '', false, true, false, false, $messageHandler);

        // Wait until all messages are received
        while ($queue[1] > 0) {
            if ($queue[1] < 1) {
                break;
            }else {
                $service->channel->wait();
            }
            $queue[1]--;
        }

        // Send all messages
        Response::send(
            [
                'success' => true,
                'Message' => 'All messages from the sms queue.',
                'data' => $messages
            ],
            200
        );

        $service->close();
    }

    // Consume single message
    public function consumeSingleMessage(RabbitMQService $service)
    {
        try {
            $service->declareExchange();
            $queue = $service->declareQueue();

            /*
             * Get only single message from the queue.
             * And ask server to mark this message as delivered as soon as the server puts it on socket.
             * If we want, here we can acknowledge the server manually after processing the message.
             */
            $message = $service->channel->basic_get(SMS_QUEUE, true);
        } catch (\Throwable $error) {
            $service->close();
            Response::send(
                [
                    'success' => false,
                    'message' => 'Sorry! Queue not found.',
                    'data' => []
                ],
                200
            );
            return;
        }

        if ($message) {
            $messageJson = json_decode($message->getBody(), true);
            Response::send(
                [
                    'success' => true,
                    'message' => 'Single message from the queue.',
                    'data' => $messageJson
                ],
                200
            );
            $service->close();
            return;
        }


        Response::send(
            [
                'success' => false,
                'message' => 'Queue is empty! No message found on the queue.',
                'data' => []
            ],
            200
        );

        $service->close();
    }

    // Get total queues size
    public function queueSize(RabbitMQService $service)
    {
        try {
            $service->declareExchange();
            $queue = $service->declareQueue();
            $message = $service->channel->basic_get(SMS_QUEUE, false);
        } catch (\Throwable $error) {
            Response::send(
                [
                    'success' => false,
                    'message' => 'Error! unable to get queue size.',
                    'data' => []
                ],
                200
            );
            return;
        }

        Response::send(
            [
                'success' => true,
                'message' => 'Queue size',
                'data' => [
                    'total' => $message ? $message->getMessageCount() + 1 : 0
                ]
            ],
            200
        );

        $service->close();
    }
}
