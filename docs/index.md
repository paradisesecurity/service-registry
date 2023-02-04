# Registry

A simple registry component that can be used for all types of php applications.

## Installation

```bash
composer require paradisesecurity/service-registry
```

## Basic usage

A registry object acts as a collection of objects. The registry allows you to store objects which implement a specific interface.

### ServiceRegistry

To create a new `ServiceRegistry` you need to define the interface that all objects will be an instance of.

This example will use the `DateTimeInterface`.

```php
use ParadiseSecurity\Component\ServiceRegistry\Registry\ServiceRegistry;

$registry = new ServiceRegistry(\DateTimeInterface::class);
```

Now you can register any object with the corresponding `DateTimeInterface` interface.

The `DateTime` and `DateTimeImmutable` objects both meet that criteria and can be registered in the `ServiceRegistry`.

```php
$registry->register('date_time', new \DateTime());
$registry->register('date_time_immutable', new \DateTimeImmutable());
```

The first parameter of of the `register` method will become the key in the array of services. Use the `ServiceRegistry` as you would any normal array.

You can manage the array using the built in controls:

```php
$registry->has('date_time'); // returns true

$registry->get('date_time'); // returns the \DateTime object you inserted earlier

$registry->all(); // returns an array containing both previously inserted objects
```

To remove a service from the registry:

```php
$registry->unregister('date_time');

$registry->has('date_time'); // now returns false
```

### PrioritizedServiceRegistry

The `PrioritizedServiceRegistry` registers objects in an array without a user provided key identifier. The `get` method is removed and all interactions with the registry are made using the method `all`.

This example will use the `DateTimeInterface`. Creating a new `PrioritizedServiceRegistry` is the same as the `ServiceRegistry`.

```php
use ParadiseSecurity\Component\ServiceRegistry\Registry\PrioritizedServiceRegistry;

$registry = new PrioritizedServiceRegistry(\DateTimeInterface::class);
```

All registered objects must still be an instance of the `DateTimeInterface` interface.

Instead of registering the objects using a key name, you will provide the object first, followed by the priority in which you want the object to be called. A default priority of `0` is given if no priority is specified.

```php
$registry->register(new \DateTime(), 5);
$registry->register(new \DateTimeImmutable());
```

In this example, the `DateTimeImmutable` object will have a lower priority over the `DateTime` object. The higher number will be called first. Suppose that all of the objects you registered have a similar method called `supports`. You can loop through your registry to see if a specific service supports a given variable.

```php
$subject = 'timezone';

foreach ($this->registry->all() as $service) {
    if ($service->supports($subject)) {
        return $service;
    }
}
```