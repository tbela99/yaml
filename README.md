## PHP Yaml Parser and Dumper

Based on the Symphony Yaml Component

## Modify Yaml And Preserve Comments

```yaml
# this is a comment
version: "3.8"
services:
  redis:
    image: redis:alpine
    ports:
      - "6379"
    networks:
      - frontend
    deploy:
      replicas: 2
      update_config:
        parallelism: 2
        delay: 10s
      restart_policy:
        condition: on-failure

  db:
    image: postgres:9.4
    volumes:
      - db-data:/var/lib/postgresql/data
    networks:
      - backend
    deploy:
      placement:
        constraints:
          - "node.role==manager"

networks:
  frontend:
  backend:

volumes:
  db-data:
# this is the last comment
```

```php

$parser = new Parser();

$data = $parser->parse($yaml);
$ast = $parser->getAst();

var_dump(isset($ast['services.redis.image'])); // true

echo $ast['services.redis.image']; // redis:alpine

unset($ast['services']);

echo $ast;
```

```yaml
# this is a comment
version: 3.8
networks:
  frontend:
  backend:
volumes:
  db-data:
# this is the last comment
```

```php

$ast['service.db'] = [
  'image' => 'mariadb',
  'environment' => [
    'NODE' => true,
    'SIZE' => '2G'
  ]
];

$ast['service.db.image']->addComment('mariadb image?');
$ast['service.db.environment']->addComment('environment variables');
$ast['service.db.environment.NODE']->addComment('killer app');
$ast['service.db.environment.SIZE']->addComment('going big here ...');

echo $ast;
```

```yaml
# this is a comment
version: 3.8
networks:
  frontend:
  backend:
volumes:
  db-data:
# this is the last comment
service:
  db:
    # mariadb image?
    image: mariadb
    # environment variables
    environment:
      # killer app
      NODE: true
      # going big here ...
      SIZE: 2G
```

```php

$ast['service.db.environment.NODE'] = false;
$ast['service.db.environment.NODE']->setComments(['killer bee']);
$ast['service.db.environment.SIZE'] = '4G';
$ast['service.db.environment.RANGE'] = '100m';

echo $ast;
```

```yaml
# this is a comment
version: 3.8
networks:
  frontend:
  backend:
volumes:
  db-data:
# this is the last comment
service:
  db:
    # mariadb image?
    image: mariadb
    # environment variables
    environment:
      # killer bee
      NODE: false
      # going big here ...
      SIZE: 4G
      RANGE: 100m
```

```php

// binary value
$ast['service.db.environment.BIN'] = hex2bin('abcf');
// multiline string
$ast['service.db.environment.secret'] = "correct horse 
battery staple";
```

```yaml
# this is a comment
version: 3.8
networks:
  frontend: 
  backend: 
volumes:
  db-data: 
# this is the last comment
service:
  db:
    # mariadb image?
    image: mariadb
    # environment variables
    environment:
      # killer bee
      NODE: false
      # going big here ...
      SIZE: 4G
      RANGE: 100m
      BIN: !!binary q88=
      secret: "correct horse \nbattery staple"
```

## Get parsed data

```php

/**
 * @var array $data
 */

$data = $ast->getData();
```
 
## manipulate Data

```php

/**
 * @var array $data
 */

$ast['version'] = 3.7;
$ast['author'] = 'a random guy';
$ast['authors.list'] = ['John', 'Raymond', 'Michael'];
```
 ```yaml
# this is a comment
version: 3.7
networks:
  frontend: 
  backend: 
volumes:
  db-data: 
# this is the last comment
service:
  db:
    # mariadb image?
    image: mariadb
    # environment variables
    environment:
      # killer bee
      NODE: false
      # going big here ...
      SIZE: 4G
      RANGE: 100m
      BIN: !!binary s6P2WISy5A2WFdyUiHtxemncqdEBpVT+JQm2g5fCtN8=
      secret: "correct horse \nbattery staple"
author: 'a random guy'
authors:
  list:
    - John
    - Raymond
    - Michael
```

```php

unset($ast['service.db']);
echo $ast;
```

```yaml
# this is a comment
version: 3.7
networks:
  frontend: 
  backend: 
volumes:
  db-data: 
# this is the last comment
service:

author: 'a random guy'
authors:
  list:
    - John
    - Raymond
    - Michael

```

## Disclaimer

- I have not tested the changes beyond what I needed to support

feel free to hack and eventually send a pull request.

