# Registration of handlers in message buses

* Status: accepted
* Date: 2020-11-12

## Context and Problem Statement

While defining new handlers, we may define a bus to which particular handler should be assigned to. If not, handler will
be added to all existing buses. Right now, we have only one bus in Sylius, so decision will not change current behaviour.
However, we may add additional buses in the future and more than one bus is not uncommon thing.

## Decision Drivers <!-- optional -->

* Driver 1 - easiness of changing default behaviour and usage of the bus
* Driver 2 - backward compatible possibility of changing behaviour

## Considered Options

### Automatically register handlers in all buses

* Good, because end users does not have to care, which bus is injected.
* Bad, because it may lead to registration of handlers in buses, where they should not be registered. Furthermore, it would
be hard to explicitly remove them from this particular bus.
* Bad, because changing this behaviour would result in backward compatibility break.

### Explicitly declare, where bus should be registered

* Good, because Sylius will contain its logic in pre-defined place.
* Good, because user may opt in, if some handler would be needed in another bus (by redeclaring/aliasing existing handler with a new tag).
* Good, because this behaviour is more predictable (we always sure about intention of which bus should be used).
* Good, because we can easily change this behaviour in the future, to register handlers everywhere without breaking existing apps.
* Bad, because end users needs to inject `sylius.default_bus` in order to use Sylius commands. 

## Decision Outcome

Chosen option: "[Explicitly declare, where bus should be registered]", because this options leaves the most options to end user
without forcing him to use them. What is more, we may easily adjust this option in the future

## References <!-- optional -->

* [Symfony documentation about buses](https://symfony.com/doc/current/messenger/multiple_buses.html#restrict-handlers-per-bus)
