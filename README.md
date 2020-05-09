## PHP Yaml Parser and Dumper

Based on the Symphonie Yaml Component

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
/**
# this is a comment
version: 3.8
networks:
  frontend:
  backend:
volumes:
  db-data:
# this is the last comment
*/

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

/**
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
*/

$ast['service.db.environment.NODE'] = false;
$ast['service.db.environment.NODE']->setComments(['killer bee']);
$ast['service.db.environment.SIZE'] = '4G';
$ast['service.db.environment.RANGE'] = '100m';

echo $ast;

/**
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

*/

```

## Disclaimer

- I have not tested the changes beyond what I needed

feel free to hack and eventually send a pull request.

## Resources

The Yaml component loads and dumps YAML files.

- [Documentation](https://symfony.com/doc/current/components/yaml.html)
- [Contributing](https://symfony.com/doc/current/contributing/index.html)
- [Report issues](https://github.com/symfony/symfony/issues) and
  [send Pull Requests](https://github.com/symfony/symfony/pulls)
  in the [main Symfony repository](https://github.com/symfony/symfony)
