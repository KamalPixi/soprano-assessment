<?php

/**
 * App Configuration file
 */

// RabbitMQ Configuration
define('RMQ_HOST', 'soprano_rabbitmq');
define('RMQ_PORT', 5672);
define('RMQ_USER', 'sopranoRabbitmqUser');
define('RMQ_PASSWORD', 'sopranoRabbitmqPassword');
define('RMQ_VHOST', '/');
define('SMS_EXCHANGE_TYPE', 'direct');
define('SMS_EXCHANGE', 'sms.direct');
define('SMS_QUEUE', 'sms_queue');
define('SMS_QUEUE_ROUTING_KEY', 'sms');
