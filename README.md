<p align="center"><a href="https://see.asseco.com" target="_blank"><img src="https://github.com/asseco-voice/art/blob/main/evil_logo.png" width="500"></a></p>

# Asseco Attachment

Purpose of this repository is to provide attachment support for any Laravel model. 

## Installation

Require the package with ``composer require asseco-voice/laravel-attachments``.
Service provider will be registered automatically.


Publish configurations and migrations, then migrate comments table.

``` bash
$ php artisan vendor:publish
$ php artisan migrate
```



Add `Attachable` trait to your commentable model(s).

``` php
use Asseco\Attachments\Contracts\Attachable;

class Product extends Model implements Attachable
{
    use Attachable;
    
    // ...   
}
