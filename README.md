## PHP Yaml Parser and Dumper

Based on the Symphony Yaml Component

## Installation

```bash
$ composer require tbela99/yaml
```

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

use Symfony\Component\Yaml\Ast\Node;

$ast = new Node();

$data = $ast->parse($yaml);

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

$data = $ast->getValue();
```

## Manipulate Data

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
author: "a random guy"
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

author: "a random guy"
authors:
  list:
    - John
    - Raymond
    - Michael
```

## Parsing Yaml

```php
use Symfony\Component\Yaml\Ast\Node;
use Symfony\Component\Yaml\Ast\Value;

$ast = new Node();

// parse Yaml string
$ast->parse($yaml);
//or parse Yaml file
$ast->parseFile($file);

// alter the ast
$ast['version'] = '1.0';
$ast['version']->addComment('this comment is associated to the version number');
// or
$ast->appendValue(1.0, 'version', ['this comment is associated to the version number']);
// or
$ast->appendNode(new Value(1.0), 'version', ['this comment is associated to the version number']);

// render the ast
$yaml = (string) $ast;

// do something useful with the output
file_put_contents('configuration.yaml', $yaml);
```

## Using '.' In Key

By default '.' is used and path delimiter. You must escape the key to avoid interpretation

```php

$ast = new Node();

$ast['path.to.data'] = "user name";

// use '.' in the key name
$ast[$ast->escapeKey('v0.1')] = [

  'description' => 'first stable release',
  'download' => 'https://example.com'
];

$ast['versions'] = [
  'v0.1' => [

    'description' => 'first stable release',
    'download' => 'https://example.com'
  ]
];

echo $ast;
```

result

```yaml
path:
  to:
    data: "user name"
v0.1:
  description: "first stable release"
  download: "https://example.com"
versions:
  v0.1:
    description: "first stable release"
    download: "https://example.com"
```
