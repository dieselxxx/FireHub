---
layout: default
title: Collection
parent: Support
nav_order: 1
---
# Collection

- [# Introduction](#-introduction)
- [# Basic Collection](#-basic-collection)
- - [# Creating Basic Collection](#-creating-basic-collection)
- - [# Passing array to Collection](#-passing-array-to-collection)
- [# Index Collection](#-index-collection)
- - [# Creating Index Collection](#-creating-index-collection)
- [# Lazy Collection](#-lazy-collection)
- - [# Creating Lazy Collection](#-creating-lazy-collection)
- [# Object Collection](#-object-collection)
- - [# Creating Object Collection](#-creating-object-collection)
- [# Iterating Over Collection](#-iterating-over-collection)
- [# Method Listing](#-method-listing)
- - [# all](#-all)
- - [# count](#-count)
- - [# setSize](#-setsize)

## # Introduction

Collection is a wrapper for creating and managing list of data like arrays, objects, files etc.

FireHub offers lots of different collection types you can work with. Some of them are more focused
on speed,  some on memory consumption and some are specialized to handle special data types like
objects, files etc.

Once you instantiate `Collection` class you will be presented with a couple of static method that
represents different collection types.

All collections currently in FireHub are considered to be _lazy_, means that function in `Collection`
static method will not fill collection entities until you actually need them or ask for them.
Our collections in examples bellow won't produce any results until we ask for collection items or try to do
some other function on top of our initial function.

## # Basic Collection

Basic Collection type is collection that has main focus of performance and doesn't concern
itself about memory consumption
This collection can hold any type of data.

### # Creating Basic Collection

Basic Collection, or sometime called Array Collection can be instantiated when calling `create` static
method.
`create` method accepts only one argument, anonymous or arrow function.

Thing to remember is that anonymous function requested by the `create` method must always return array.

Let's try to create Basic Collection from list of numbers.


```php
use FireHub\Support\Collections\Collection;

$collection = Collection::create(function ():array {
    for($i = 0; $i < 1_000_000; $i++) {
        $list[$i] = $i++;
    }
    return $list ?? [];
});
```

### # Passing array to Collection

If you already have an array that you just want to pass it to collection and use collection features
on it, you can do it like on the example bellow.

This is good example if you have small array, and you just need to have all the features that collection
offers, but if you have large array it is always better to try to create array inside the `create`
method.

```php
use FireHub\Support\Collections\Collection;

$example_array = [
'firstname' => 'John',
'lastname' => 'Doe',
'age' => 25
];

// anonymous style collection
$collection = Collection::create(function () use ($example_array):array {
    return $example_array;
});

// arrow function style collection
$collection = Collection::create(fn ():array => $example_array);
```

## # Index Collection

Index Collection allows only integers as keys, but it is faster and uses less memory than
basic collection.  
This collection type must be resized manually and allows only integers within the range
as indexes.

### # Creating Index Collection

Index Collection can be instantiated when calling `index` static method.  
`index` method accepts two arguments, anonymous or arrow function and size argument.

Anonymous function requested by the `index` method should not return any results.
Inside out anonymous function parameter `$items` represents [SplFixedArray](https://www.php.net/manual/en/class.splfixedarray),
which you can type-hint to get more support from your IDE.  
Adding more data to you Index Collection is like adding to any kind of normal PHP array using `$items[$key] = $value`.

Size argument is required and lets you change the size of an array to the new size of size. If size is less than the current array size,
any values after the new size will be discarded. If size is greater than the current array size,
the array will be padded with null values.

Let's try to create Index Collection from list of numbers.


```php
use FireHub\Support\Collections\Collection;

$collection = Collection::index(function ($items):void {
    for($i = 0; $i < 1_000_000; $i++) {
        $items[$i] = $i;
    }
}, size: 1_000_000);
```

## # Lazy Collection

Lazy Collection uses to power of [PHP Generators](https://www.php.net/manual/en/language.generators.overview.php)
and allow you to work with very large datasets while keeping memory usage low.

While it will keep memory usage low at any array size, it will take a performance hit while
doing so.

### # Creating Lazy Collection

Lazy Collection can be instantiated when calling `lazy` static method.  
`index` method accepts two arguments, anonymous or arrow function and size argument.

Anonymous function requested by the `lazy` method should return PHP Generator.

Let's try to create Lazy Collection from list of numbers.


```php
use FireHub\Support\Collections\Collection;

$collection = Collection::lazy(function ():Generator {
    for($i = 0; $i < 1000000; $i++) {
        yield $i;
    }
});
```

## # Object Collection

While any collection can store objects, Object collection is specialized to store large amount of them.

### # Creating Object Collection

Object Collection can be instantiated when calling `object` static method.  
`index` method accepts two arguments, anonymous or arrow function and size argument.  
Adding more data to you Object Collection is done by using method `attach()` like so:  
`$items->attach(new class {}, $info_data_for_that_object);`.

Anonymous function requested by the `object` method should not return any results.
Inside out anonymous function parameter `$items` represents [SplObjectStorage](https://www.php.net/manual/en/class.splobjectstorage),
which you can type-hint to get more support from your IDE.

Let's try to create Object Collection from list of numbers.


```php
use FireHub\Support\Collections\Collection;

$collection = Collection::object(function ($items):void {
    for($i = 0; $i < 1_000; $i++) {
        $items->attach(new class {}, $i);
    }
});
```

## # Iterating Over Collection

Since our collections are _lazy_ and don't produce any results while we crete them, one way to invoking
them is to iterate over them.

You can iterate over any collection just like you would with any other normal PHP array,
using loops `foreach`, `for`, `while` etc.

```php
use FireHub\Support\Collections\Collection;

$collection = Collection::create(fn ():array => [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'age' => 25
]);

foreach ($collection as $key => $value) {
    echo "key = $key, value = $value; ";
}

// result:
// key = firstname, value = John; key = lastname, value = Doe; key = age, value = 25; 
```

## # Method Listing

Bellow is a list of all available methods you can use on the collections.

Not all collection types will have available all these methods, so we will list all collection that
can use each method in separate table.

### # all

> Available on collection:
>> Basic | Index | Lazy | Object
>> :---:|:---:|:---:|:---:
>> yes | yes | yes | yes

Method all gives you ability to read underlying array represented of the collection.

This method is discouraged to use in production because it will revert your collection
back into normal PHP array, and you will get performance hit out of it.  
Instead, you can use this method to debug your collection.

```php
$collection = Collection::create(fn ():array => [1,2,3]);

$result = $collection->all();

print_r($result);

// result:
// Array ( [0] => 1 [1] => 2 [2] => 3 ) 
```

### # count

> Available on collection:
>> Basic | Index | Lazy | Object
>> :---:|:---:|:---:|:---:
>> yes | yes | yes | yes

Count method counts all items inside collection.

You can count items in two different ways:

- using count method

```php
$collection = Collection::create(fn ():array => [1,2,3]);

echo $collection->count();

// result:
// 3
```

- using count function

```php
$collection = Collection::create(fn ():array => [1,2,3]);

echo count($collection);

// result:
// 3
```

### # setSize

> Available on collection:
>> Basic | Index | Lazy | Object
>> :---:|:---:|:---:|:---:
>> no | yes | no | no

setSize you change the size of the Index Collection to the new size. If size is less than the current array size,
any values after the new size will be discarded. If size is greater than the current array size,
the array will be padded with null values.

```php
$collection = Collection::index(function ($items):void {
    $items[0] = 0;
    $items[1] = 1;
    $items[2] = 2;
}, size: 3);

echo count($collection);

// result:
// 3

$collection->setSize(10);

echo count($collection);

// result:
// 10

print_r($collection->all());

// result:
// Array ( [0] => 0 [1] => 1 [2] => 2 [3] => [4] => [5] => [6] => [7] => [8] => [9] => ) 
```
