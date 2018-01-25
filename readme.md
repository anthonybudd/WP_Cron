# WP_Model_Slim - Pseudo ORM for WordPress Plugins

<p align="center"><img src="https://c1.staticflickr.com/1/415/31850480513_6cf2b5bdde_b.jpg"></p>

### A simple class for creating active record models of WordPress Posts.
WP_Model_Slim is a trimmed down version of [WP_Model](https://github.com/anthonybudd/WP_Model), a pseudo ORM for WordPress designed to provide a better method for handling posts using a simple OOP style syntax. WP_Model_Slim has been built for use in Plugin where the full functionility of WP_Model is not required.

#### Introduction: **[Medium Post](https://medium.com/@AnthonyBudd/wp-model-6887e1a24d3c)**

#### WP_Model Full Class: **[GitHub](https://github.com/anthonybudd/WP_Model)**


```php

Class Product extends WP_Model_Slim
{
    public $postType = 'product';
    public $attributes = [
        'color',
        'weight'
    ];
}

Product::register();

$book = new Product;
$book->title = 'WordPress for dummies';
$book->color = 'Yellow';
$book->weight = 100;
$book->save();

```

# Installation

Require WP_Model with composer

```
$ composer require anthonybudd/WP_Model
```

**Or**

Download the WP_Model class and require it at the top of your functions.php file. This is not recommended. 


# Setup
You will then need to make a class that extends WP_Model_Slim. This class will need the public property $postType and $attributes, an array of strings.
```php
Class Product extends WP_Model_Slim
{
    public $postType = 'product';

    public $attributes = [
        'color',
        'weight'
    ];
    
    public $prefix = 'wp_model_'; // Optional
}
```
If you need to prefix the model's data in your post_meta table add a public property $prefix. This will be added to the post meta so the attribute 'color' will be saved in the database using the meta_key 'wp_model_color'


# Register
Before you can create a post you will need to register the post type. You can do this by calling the static method register() in your functions.php file.
```php
Product::register();

Product::register([
    'singular_name' => 'Product'
]);
```
Optionally, you can also provide this method with an array of arguments, this array will be sent directly to the second argument of Wordpress's [register_post_type()](https://codex.wordpress.org/Function_Reference/register_post_type) function.


# Creating and Saving
You can create a model using the following methods.
```php
$product = new Product();
$product->color = 'white';
$product->weight = 300;
$product->title = 'the post title';
$product->content = 'the post content';
$product->save();

$product = new Product([
    'color' => 'blue',
    'weight' => '250'
]);
$product->save();

$product = Product::insert([
    'color' => 'blue',
    'weight' => '250'
]);
```


# Retrieving Models
**find()**

find() will return an instantiated model if a post exists in the database with the ID if a post cannot be found it will return NULL.

```php
$product = Product::find(15);
```

**all()**

all() will return all posts. Use with caution.

```php
$allProducts = Product::all();
```

**in()**

To find multiple posts by ID you can us the in() method.

```php
$firstProducts = Product::in([1, 2, 3, 4]);
```

# Deleting
**delete()**

delete() will trash the post.

```php
$product = Product::find(15);
$product->delete();
```
**restore()**

restore() will unTrash the post and restore the model. You cannot restore hardDeleted models.

```php
$product = Product::restore(15);
```

**hardDelete()**

hardDelete() will delete the post and set all of it's meta (in the database and in the object) to NULL.

```php
$product->hardDelete();
```

# Helper Properties

```php
$product->title; // Returns the post's title

$product->content; // Returns the post's content

$product->the_content; // Returns the post's content via the 'the_content' filter
```


# Helper Methods

```php
Product::single(); // Returns the current model if on a single page or in the loop

Product::exists(15); // Returns (bool) true or false

Product::latest(); // Returns the most recent post

Product::latest(10); // Returns the most recent 10 posts array(Product, Product, Product, Product)

Product::count($postStatus = 'publish'); // Efficient way to get the number of models (Don't use count(WP_Model::all()))

$product->postDate($format = 'd-m-Y'); // Returns the post date based on the format supplied

$product->get($attribute, $default) // Get attribute from the model

$product->set($attribute, $value) // Set attribute of the model

$product->post() // Returns the WP_Post object (This will be the post at load, any updates to the post (title, content, etc) will not be reflected)

$product->permalink() // Returns the post permalink

$product->hasFeaturedImage() // Returns TRUE if a featured image has been set or FALSE if not

$product->featuredImage($defaultURL) // Returns the featured image URL

$product->toArray() // Returns an array representation of the model

Product::asList() // Returns array of posts keyed by the post's ID
[
    15 => Product,
    16 => Product,
    17 => Product
]

// You can also specify the value of each element in the array to be meta from the model.
Product::asList('post_title')
[
    15 => "Product 1",
    16 => "Product 2",
    17 => "Product 3"
]
```

# Virtual Properties
If you would like to add virtual properties to your models, you can do this by adding a method named the virtual property's name prefixed with '_get'

```php

Class Product extends WP_Model_Slim{
    ...

    public $virtual = [
        'humanWeight'
    ];

    public function _getHumanWeight()
    {  
        return $this->weight . 'Kg';
    }
}

$product = Product::find(15);
echo $product->humanWeight;
```

# Default Properties
To set default values for the attributes in your model use the $default property. The key of this array will be the attribute you wish to set a default value for and the value will be the default value.

```php

Class Product extends WP_Model_Slim
{
    ...

    public $default = [
        'color' => 'black'
    ];
}

$product = new Product;
echo $product->color; // black
```

**finder()**

The finder() method allows you to create a custom finder method, this is the best way to contain frequently used WP_Querys inside your model's class. To create a custom finder first make a method in your model named your finders name and prefixed with '_finder', this method must return an array. The array will be given directly to the constructor of a WP_Query. The results of the WP_Query will be returned by the finder() method. You can provide additional arguments to the finder method by providing an array to the second argument of the static method finder() as shown below ('heavyWithArgs').

If you would like to post-process the results of your custom finder you can add a '_postFinder' method. This method must accept one argument which will be the array of found posts.

```php

Class Product extends WP_Model_Slim
{
    ...

    public function _finderHeavy($args)
    {  
        return [
            'meta_query' => [
                [
                    'key' => 'weight',
                    'compare' => '>',
                    'type' => 'NUMERIC',
                    'value' => '1000'
                ]
            ]
        ];
    }

    // Optional
    public function _postFinderHeavy($results)
    {  
        return array_map(function($model){
            if($model->color == 'green'){
                return $model->color;
            }
        }, $results);
    }


    // Finder with optional args
    public function _finderHeavyWithArgs($args)
    {  
        return [
            'paged'      => $args['page'], // 3
            'meta_query' => [
                [
                    'key' => 'weight',
                    'compare' => '>',
                    'type' => 'NUMERIC',
                    'value' => '1000'
                ]
            ]
        ];
    }
}

$heavyProducts = Product::finder('heavy');

// Finder with optional args
$heavyProducts = Product::finder('heavyWithArgs', ['page' => 3]); 
```

# Events
WP_Model has an events system, this is the best way to hook into WP_Model's core functions. All events with the suffix -ing fire as soon as the method has been called. All events with the suffix -ed will be fired at the very end of the method. Below is a list of available events. All events will be supplied with the model that triggered the event

You can also trigger the save, insert and delete events from the admin section of wordpress.

- booting
- booted
- saving
- inserting
- inserted
- saved
- deleting
- deleted
- hardDeleting
- hardDeleted
- serializing

When saving a new model the saving, inserting, inserted and saved events are all fired (in that order).

```php
Class Product extends WP_Model_Slim
{
    ...
    
    public function saving(){
        echo "The save method has been called, but nothing has been written to the database yet.";
    }
    
    public function saved($model){
        echo "The save method has completed and the post and it's meta data have been updated in the database.";
        echo "The Model's ID is". $model->ID;
    }
}
```
