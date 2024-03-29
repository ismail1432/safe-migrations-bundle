# Safe Migrations Bundle

----------------

### ⚠️ Warn you when your **auto generated** doctrine migrations contains unsafe SQL statements.

An unsafe migration is:
- An operation that have to be done carefully if you are doing zero downtime deployments.
- An operation on a critical table defined by yourself.
- An operation that can lock table such like `NOT NULL CONSTRAINT` or loss data such like remove or truncate.
- An operation that can be dangerous such like `DROP` or `RENAME`.
- An operation defined by yourself.

When an unsafe migration is detected, a warning is displayed in the command `doctrine:migrations:diff` and a comment is added into the migration file.

![image](https://user-images.githubusercontent.com/13260307/237054338-1b1412e3-6f24-4e05-b929-15c30ab0a736.png)

![image](https://user-images.githubusercontent.com/13260307/237054375-44fe19b1-9915-4841-b366-9ac83d76e360.png)


### Unsafe statements list

- CREATE INDEX
- DROP
- MODIFY
- NOT NULL
- RENAME
- TRUNCATE

Any of these statement present in your last migration will trigger a warning, feel free to submit a PR to add more statements.

### Features

- [You can exclude a statement](#exclude-a-statement)
- [You can add your own statements](#create-your-own-statement)
- [You can flag a table as critical to be warned when a migration contains changes on these tables](#configure-critical-tables)
- [You decorate a statement to personalize the warning message](#decorate-a-statement)

## Getting started
### Installation
You can easily install Safe Migrations Bundle by composer
```
$ composer require eniams/safe-migrations --dev
```
Then, bundle should be registered. Just verify that `config\bundles.php` is containing :
```php
Eniams\SafeMigrationsBundle\SafeMigrationsBundle::class => ['dev' => true],
```

### Configuration
Then, you should register it in the configuration (`config/packages/dev/safe_migrations.yaml`) :
```yaml
# config/packages/safe-migrations.yaml
    safe_migrations:
      # required
      migrations_path: '%kernel.project_dir%/migrations'
      # optional
      critical_tables: # List of critical tables
        - 'user'
        - 'product'
        - # ...
      # optional
      excluded_statements: # List of operations that not need a warning
        - 'TRUNCATE'
        - # ...
```

##### Exclude a statement
If you want to exclude a statement, you can do it by adding it in the configuration file.

```yaml
# config/packages/safe-migrations.yaml
    safe_migrations:
      excluded_statements: # List of operations that not need a warning
        - 'TRUNCATE' # The statement TRUNCATE will not be flagged as unsafe
        - # ...
```


##### Create your own statement
If you want to create a custom statement, you can do it by adding a new class that implements `Eniams\SafeMigrationsBundle\Statement\StatementInterface`.

###### Here is an example
```yaml
# config/services.yaml
services:
    _defaults:
        autoconfigure: true
``` 

```php
<?php
namespace App\Statement\MyStatement;
use Eniams\SafeMigrationsBundle\Statement\StatementInterface;

class MyStatement implements StatementInterface
{
    protected string $migrationWarning;

    public function migrationWarning(): string
    {
        // The message that will be added in the migration file
        return $this->migrationWarning;
    }
    
    public function supports(string $migrationUpContent): bool
    {
        // The logic to determine if the statement is present in the `up` method of migration file.
        // The following code can be enough
        return str_contains(strtoupper($statement), $this->getStatement());
    }

    public function getStatement(): string;
    {
        return 'MY_STATEMENT';
    }
}
```
##### Configure critical tables
If you want to flag a table as critical and be warned when a migration contains changes on it, just flag the tables like this:

```yaml
# config/packages/safe-migrations.yaml
    safe_migrations:
      critical_tables: # List of critical tables
        - 'user'
        - 'product'
        - # ...
```

##### Decorate a statement
If you want to wrap a statement to personalize the warning message or the logic to catch the statement you can use the [decorator design pattern](https://en.wikipedia.org/wiki/Decorator_pattern#PHP).

See the example bellow, you can also check [how to decorate a service with Symfony](https://symfony.com/doc/current/service_container/service_decoration.html).

```php
<?php
namespace App\Statement;

use Eniams\SafeMigrationsBundle\Statement\AbstractStatement;

class CustomNotNullStatement extends AbstractStatement
{
    public function getStatement(): string
    {
        return 'NOT NULL';
    }

    public function migrationWarning(): string
    {
        return 'Your custom message';
    }
}
```

```yaml
# config/services.yaml
    App\Warning\CustomNotNullStatement:
      decorates: 'eniams_safe_migrations.not_null.statement'
```

##### Event Listener
When an unsafe migration is created, an event `Eniams\SafeMigrationsBundle\Event\UnsafeMigrationEvent` is dispatched, you can listen it and retrieve a `UnsafeMigration` with the migration name and the content of the migration file.

Example:

```php
<?php

namespace App\Listener;

use Eniams\SafeMigrationsBundle\Event\UnsafeMigrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrationRiskySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UnsafeMigrationEvent::class => 'onUnsafeMigration',
        ];
    }

    public function onUnsafeMigration(UnsafeMigrationEvent $event)
    {
        $unsafeMigration = $event->getUnsafeMigration();

        // Version20231030215756
        $unsafeMigration->getMigrationName();

        // Migration file
        $unsafeMigration->getMigrationFileContent();

        // Migration file with the warning.
        $unsafeMigration->getMigrationFileContentWithWarning();
    }
}
```

##### Debug the configuration

You can debug the configuration you set with the following command:
`$ bin/console eniams:debug-configuration`

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

After writing your fix/feature, you can run following commands to make sure that everything is still ok.

```bash
# Install dev dependencies
$ composer install

# Running tests and quality tools locally
$ make all
```

## Authors
- Smaïne Milianni - [ismail1432](https://github.com/ismail1432) - <smaine(dot)milianni@gmail(dot)com>
- Quentin Dequippe - [qdequippe](https://github.com/qdequippe) - <quentin@dequippe(dot)tech>
