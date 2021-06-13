## Technical test

* Using docker with nginx, PHP8.0, and MYSQL 5.7 and the framework Symfony 4.4

First you have to bring up your docker containers:

`docker-compose up -d --build --remove-orphans`

You may have to clean cache inside docker

```/bin/console c:c```


To use the API you may use a HTTP client as POSTMAN or similar


127.0.0.1:8000/api/AddUser

In the body you must add the body content in JSON format

`{
"name": "user2",
"username": "user2",
"password": "test",
"roles": "ROLE_ADMIN"
}`
It will return 201

To update you may add the name of the user to update
127.0.0.1:8000/api/AddUser/{name}
and add in the body as JSON what do you like to change

`{
"username": "user"
}`

It will return 200

To delete user
127.0.0.1:8000/api/deleteUser/{name}
It will return 200