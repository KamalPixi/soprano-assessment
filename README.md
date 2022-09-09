## Soprano assessment - A PHP dockerize application that produce and consumes SMS messages from/to a RabbitMQ server queue.

- **Requirements**
<p>Docker</p>
<p>Docker Compose</p>

- **Building & running the application containers**
<p>docker compose up -d</p>

- **Remove containers**
<p>docker compose down</p>

- **Making request to the root url, will receive a json contains examples.**
<p>To know api using doc/data-structure - [GET] HOST-IP</p>

- **API endpoints**
<p>To send/queue an SMS - [POST] HOST-IP/queues.php</p>
<p>Ex: data structure {"to":"", "message":""}</p>

<p>To get all SMS from the queue - [GET] HOST-IP/queues.php</p>
<p>To get an SMS from the queue -  [GET] HOST-IP/queues_single.php</p>
<p>To  get total SMS currently in the queue -  [GET] HOST-IP/queues_total.php</p>
