# Services naming convention

* Status: Accepted
* Date: 2024-02-24

## Context and Problem Statement

Services in Sylius are named in different ways, depending on the time of their creation, and other services' names.
Also, some services named with using the `dot` notation are harder to be used with `autowire` feature.

## Decision Drivers

* Make naming services predictable
* Provide a consistent way of naming services
* Support the `autowire` feature

## Considered Options

### Stay with the `dot` notation

This variant is the most common way of naming services in Sylius from the beginning.
It was the only considered option in Symfony-based projects before the `autowire` feature was introduced.

* Good, because it is a common way of naming services in Symfony
* Bad, because you have to use `#[Autowire]` attribute to inject services

### Use the FQCN as a service name

This variant of naming appeared along with introducing the `autowire` feature in Symfony.
It makes using services easier, but at the same time introduces a little bit magic to the code.

* Good, because it supports the `autowire` feature
* Bad, because it requires a lot of work from the developers already using the `dot` notation
* Bad, because it is not recommended by Symfony

### Combine the `dot` notation with the FQCN (when it makes sense)

This variant is a combination of the previous two options, but it considers declaring the `FQCN` alias only when it makes sense.

* Good, because it is a recommended way by Symfony [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html#services)
* Good, because we use the `dot` notation for many services, so we do not have to rename them
* Good, because services already named with the `FQCN` will still work
* Bad, because it requires more work and increases the number of service definitions

## Decision Outcome

Chosen option: **"Combine the `dot` notation with the FQCN (when it makes sense)"**

Despite the fact that it requires more work, it is the best option to provide a consistent way of naming services and support the `autowire` feature.
Also, thanks to this approach, we stay consistent with the Symfony best practices.

Some services that are not meant to be used with the `autowire` feature should only be named with the `dot` notation:
- form types/extensions
- message handlers
- validators
- etc.

## Example

The simplest case is when we have 1:1 relation between the interface and the implementation:

```xml
<services>
    <service id="sylius_admin.resolver.some" class="Sylius\Bundle\AdminBundle\Resolver\SomeResolver" />
    <service id="Sylius\Bundle\AdminBundle\Resolver\ResolverInterface" alias="sylius_admin.resolver.some" />
</services>
```

If there are multiple services implementing the same interface:

```xml
<services>
    <service id="sylius_admin.resolver.some" class="Sylius\Bundle\AdminBundle\Resolver\SomeResolver" />
    <service id="sylius_admin.resolver.another" class="Sylius\Bundle\AdminBundle\Resolver\AnotherResolver" />
    <service id="Sylius\Bundle\AdminBundle\Resolver\ResolverInterface" alias="sylius_admin.resolver.some" /> <!-- default when the parameter name is different from belows -->
    <service id="Sylius\Bundle\AdminBundle\Resolver\ResolverInterface $someResolver" alias="sylius_admin.resolver.some" /> <!-- added just in case the default one changes -->
    <service id="Sylius\Bundle\AdminBundle\Resolver\ResolverInterface $anotherResolver" alias="sylius_admin.resolver.another" />
</services>
```

## References

* [Best Practices for Reusable Bundles](https://symfony.com/doc/current/bundles/best_practices.html#services)
