### Soprano assessment - A PHP dockerize application that produce and consumes SMS messages from/to a RabbitMQ server queue.

#### Requirements
- Docker
- Docker Compose

#### Building & running the application containers
- docker compose up -d

#### To remove containers
- docker compose down

#### Making request to the root url, will receive a json contains examples.
- List of example - [GET] HOST-IP

#### API endpoints
- To send/queue an SMS - [POST] HOST-IP/queues.php
  <p>Ex: data structure {"to":"601111085061", "message":"Hello"}</p>

- To get all SMS from the queue - [GET] HOST-IP/queues.php
- To get an SMS from the queue -  [GET] HOST-IP/queues_single.php
- To  get total SMS currently in the queue -  [GET] HOST-IP/queues_total.php
