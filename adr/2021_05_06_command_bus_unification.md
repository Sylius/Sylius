# Unification of buses in sylius

* Status: accepted
* Date: 2021-05-06

## Context and Problem Statement

Sylius and any other following product (like plugins) should utilize `sylius.command_bus` for command dispatching and `sylius.event_bus` for events.

Take into account, that the command bus requires to have one corresponding handler for each message dispatched in contradiction to the event bus (where there is no such requirement). 
In addition, the command bus will perform command validation and wrap the following handler within a transaction and flush to the database. 

At this moment we are using 2 tags `messenger.message_handler` with support for both `sylius.command_bus` and `sylius_default.bus`
