# Declaring services in container

* Status: accepted
* Date: 2022-02-11

## Context and Problem Statement

Since Symfony 3.4, all Symfony services are declared private by default. And this is the recommended Symfony approach. On the 
other hand, first stable Sylius release was published before that and therefore at the moment of publication of this recommendation
Sylius had hundreds public services. The question is how should we handle services declared after the 3.4 release.

## Decision Drivers

Declared services should be:
* easily usable in ResourceControllers (where we are allowing for fetching services directly from the container)
* easily usable as state machine callbacks, which are fetched directly for the container
* easily accessible in PHPUnit
* it would be better to have one, coherent rule for all services

## Considered Options

### Making all services public

* Good, because it is coherent with previous services
* Good, because it is easily usable with ResourceController, StateMachines and PHPUnit
* Good, because it maintains backward compatibility
* Bad, because it is against Symfony recommendation

### Making all service private

* Good, because it is coherent with previous services
* Good, because it follows Symfony recommendation
* Bad, because it breaks backward compatibility
* Bad, because it requires additional code to rework ResourceController and StateMachines service handling
* Bad, because it requires small refactor for PHPUnit handling

### Making only new services private

* Good, because it follows Symfony recommendation for new stuff
* Good, because it maintains backward compatibility
* Bad, because it requires additional code to rework ResourceController and StateMachines service handling
* Bad, because it requires small refactor for PHPUnit handling

## Decision Outcome

Chosen option: **"Making all services public"**

For now, it is the most straightforward solution, that does not cost us too much, does not put additional debt on us, and 
it is the solution that we are doing since 3.4 (it was just implicit).

## References

* [Announcement of private services](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default)
