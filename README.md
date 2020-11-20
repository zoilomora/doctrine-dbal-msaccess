# Doctrine DBAL for Microsoft Access
An implementation of the [doctrine/dbal](https://github.com/doctrine/dbal) library to support **Microsoft Access databases** in **Microsoft OS**.

There are some functionalities that are not supported by Microsoft Access in a PDO-based connection. For these functionalities the implementation uses a direct connection through ODBC.

## OS Requirements
- Microsoft Access Database Engine Redistributable ([2010](https://www.microsoft.com/download/details.aspx?id=13255) or [2016](https://www.microsoft.com/download/details.aspx?id=54920)).
- Register a **DSN** in **ODBC Data Source Administrator** (odbcad32.exe).

## Installation

1) Install via [composer](https://getcomposer.org/)

    ```shell script
    composer require zoilomora/doctrine-dbal-msaccess
    ```

### Register a **DSN**
We don't need to reinvent the wheel, on the internet there are hundreds of tutorials on how to set up a DSN for Microsoft Access.
I leave you a [video](https://www.youtube.com/watch?v=biSjA8ms_Wk) that I think explains it perfectly.

Once the DSN is configured we will have to configure the connection in the following way:

```php
$connection = \Doctrine\DBAL\DriverManager::getConnection(
    [
        'driverClass' => \ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\Driver::class,
        'dsn' => 'name of the created dsn',
    ]
);
```

## Discovered problems

### Character encoding problems
The default character encoding in Access databases is [Windows-1252](https://en.wikipedia.org/wiki/Windows-1252).
If you want to convert the data to UTF-8, a simple solution would be:

```php
$field = \mb_convert_encoding($field, 'UTF-8', 'Windows-1252');
```

If you want all the data to be encoded automatically to UTF-8 (with the performance reduction that it may imply)
configure the driver as follows:

```php
$connection = \Doctrine\DBAL\DriverManager::getConnection(
    [
        'driverClass' => \ZoiloMora\Doctrine\DBAL\Driver\MicrosoftAccess\Driver::class,
        'dsn' => 'name of the created dsn',
        'driverOptions' => [
            'charset' => 'UTF-8',
        ],
    ]
);
```

## License
Licensed under the [MIT license](http://opensource.org/licenses/MIT)

Read [LICENSE](LICENSE) for more information
