# Use raw data in commands and events

* Status: accepted
* Date: 2022-03-18

## Context and Problem Statement

While defining new commands or events we may define them with entities or any objects. Objects passed to commands or
events may desynchronize with the main object which can be causing problems with handlers that may operate on wrong data.
Passing objects to commands can ease us to implement new features and make it faster but this solution gives us plenty of
problems with integrations with external systems or eg. operating with the actual state of an object especially when
we are doing it asynchronously.

## Decision Drivers

* always operate on the actual state of the object
* ease to integrate with external systems
* to be more consistent with CQRS pattern

## Considered Options

### Passing raw data or objects to commands

* good, because it can be ease for us to implement new features
* bad, because it can be problematicaly when we are using async communication
* bad, because it can be hard to integrate commands written like that with extenal systems

### Passing only raw data or identifiers of the object(s) we want to change etc.

* good, because there is no problem with desynchronizing data while async communication
* good, because we can easy integrate with extenal systems
* good, because it's more consistent with CQRS pattern
* bad, because we need to always make queries to DB to get objects we want to modify (but we are caching queries from 
DB when we are using doctrine as our ORM)

## Decision outcome

Chosen option: **"Passing only raw data or identifiers of the object(s) we want to change etc."**

This option gives us plenty of advantages with only a small problem with always making queries when we want to work 
with DB objects.
