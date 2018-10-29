# Console staff manager

PHP based console application to manage staff.
Uses CSV file as a database.

## Installation

### Requirements
* php >= 7.1
* composer

`$ composer install`

## Usage

```php
$ php console.php [command] [[arguments]]


            Console Staff Management Tool!

            Usage:
                php console.php [command] [[arguments]]

            Commands:
                help
                view
                viewall
                insert
                update [id]
                delete [id]
                edit [id]
                import [file.csv]


```

## Tests
```
$ vendor\bin\phpunit
```

## Things to improve
* code fully covered with unit tests
* move validation mechanism away from entity setters
* add logging
* think of better duplicates checking mechanism then `CsvDatabaseManager` -> `checkDuplacates`

## License
[MIT](https://choosealicense.com/licenses/mit/)