# Laravel Flex Properties

## Introduction
With this package you can define custom attributes for Laravel's 
Eloquent models.

## Installation
You can install the package via composer:

```bash
composer require tkivelip/laravel-flex-properties
```

## How to use?

### Set up the model

#### Simple setup
The easiest to use this package it to extended the shipped model. 
Anything you need is already setted up. Now you can customize your
flex attributes by creating a property called 'flex_properties';

```php
namespace Example;

use tkivelip\LaravelFlexProperties\Model;

class ExampleModel extends Model
{
    protected $flex_properties = [
        'title'   => 'string',
        'text'    => 'text',
        'data'    => 'json',
    ];
}
```

The `$flex_properties` keys are the names of your properties. As value you 
have to set up a valid flex property type. You can add or change flex 
property types in the main config file `config/flex-properties.php` 

#### Publish Config and Migrations
If you like to custoize the configuration or the migrations, you can 
publish these files to your local installation. Same old story. Use 
any terminal, go to your project root dir and type:

```batch
php artisan vendor:publish
```

#### Extended Setup
Coming soon...


## Using Flex Properties

Flex properties are working with all usual Eloquent functions like `fill()`, 
`update()`, `create()`, etc. They have their own type based database tables, 
so there is no need to update your migrations, if you change your flex property
configuration.


### Setting Values

After you have setted up your eloquent model configuration, you can set flex
properties like any other model attributes. Here are some examples:

```php
namespace Example;

// You can use make() to fill flex properties
$model = ExampleModel::make([
    'title'       => 'Example',
    'description' => 'Look at this fill() example' 
]);

// Or set a property directly
$model->title = 'Overrides example';

// Or use fill() on an existing object  
$model->fill([
    'title'       => 'Example',
    'description' => 'Look at this fill() example' 
]);

// Don't forget to save you changes
ExampleModel->save();
```

> Note: That you have to configure `$fillable` and/or `$guarded` property to 
> allow mass assignment. Take a look at Laravel's 
>  [Mass Assignment Documentation](https://laravel.com/docs/5.7/eloquent#mass-assignment)
> for more details.

### Getting Values

You can direct access the value as a property like any other Eloquent attributes.

```$php
namespace Example;

$model = ExampleModel::create([
    'title'       => 'Example',
    'description' => 'Look at this fill() example' 
]);

echo $model->title;
echo $model->description;
```

### Appending Flex Properties

You can also use Eloquent's append function to auto append flex properties to
your model:

```php
namespace Example;

use tkivelip\LaravelFlexProperties\Model;

class ExampleModel extends Model
{
    protected $flex_properties = [
        'title'   => 'string',
        'text'    => 'text',
    ];
    
    protected $appends = [
        'title',
        'text',
    ];
}
```

### Create and Update Models

Creating and updating models is very easy. Just use the usual Eloquent methods. 

```php
namespace Example;

$example = ExampleModel::create([
    'title'       => 'Example',
    'description' => 'Look at this fill() example' 
]);

$example->update([
    'title'       => 'New title',
    'description' => 'A more senceless description' 
]);
```

### Localization

Flex properties have an implemented localization mechanism. So you can set, get 
and query flex properties in any language you like. Just use the `locale()` method
to change the locale at any time. 

> __IMPORTANT NOTE:__
> The `locale()` method changes only the flex property locale. This will not change
> your application enviorment.

```php
// Set the english title
$model->locale('en')->title = 'Example';
/
/ Set the german title
$model->locale('de')->title = 'Beispiel';

// Or fill frensh translations  
$model->locale('fr)->fill([
    'title'       => 'Exemple',
    'description' => 'Parlez-vous français?' 
]);
```

> Note: If you do not set up a locale, the default locale is used. If you  
> manually changed the locale, it will be saved on the model for the current lifecycle, 
> but not in the persistence layer. 
 

### Flex Helper

You can find some helpfull tools in the `Flex` helper class.
For example we can use the `typeExists()` method to determaniate, 
if a flex property type is configured:

```php
namespace Example;

use tkivelip\LaravelFlexProperties\Flex;

Flex::typeExists('string'); // returns true
``` 

### Querying Flex Properties

You can use the `Flex::where()`helper to extend the usual
query builder functions like where(), orWhere(), andWhere(), etc.
It takes the flex property name as first parameter. All other
parameters are equal to the eloquent builder methods. 

```php
namespace Example;

ExampleModel::where(
    Flex::where('property_name', $value);  
);

ExampleModel::where(
    Flex::where('description', 'LIKE', 'Starts with%');  
);
``` 

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [tkivelip](https://github.com/tkivelip)
- [All Other Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
 
