# Unification of base message buses in sylius

* Status: accepted
* Date: 2024-29-08

## Context and Problem Statement

Due to historical reasons, and being BC compliant, we had duplications with our message buses. 
We had `sylius.command_bus` and `sylius_default.bus` for commands, `sylius.event_bus` and `sylius_default.bus` for events.

## Decision Drivers

* Unification of the message buses
* Simplification of configuration and usage
* Lesser cognitive load for developers

## Decision Outcome

Keep only one message bus per context.
Users shouldn't have to think about which bus to use.
In conclusion anyone using Sylius should use:
 - `sylius.command_bus` for commands
 - `sylius.event_bus` for events
 - `sylius.query_bus` for queries
