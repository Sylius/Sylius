# Use raw data in commands and events

* Status: accepted
* Date: 2022-03-18

## Context and Problem Statement

While working with commands and handlers, we are always working with objects; therefore, these objects need to be 
present in handlers. We may either fetch them from the repository based on identifiers or pass them as a part of 
commands. The selected solution needs to take into account that our commands may be dispatched to external systems or 
processed asynchronously

## Decision Drivers

* always operate on the actual state of the object
* ease to integrate with external systems
* be more consistent with CQRS pattern

## Considered Options

### Passing raw data or objects to commands

Objects passed to commands or events may desynchronize with the main object which can be causing problems with handlers 
that may operate on wrong data. Passing objects to commands can ease us to implement new features and make it faster but 
this solution gives us plenty of problems with integrations with external systems or eg. operating with the actual state 
of an object especially when we are doing it asynchronously.

* good, because it can be easier for us to implement new features
* bad, because the command handler can work on not synchronized object
* bad, because it can be problematic when we are using async communication
* bad, because it can be hard to integrate commands written like that with external systems

### Passing only raw data or identifiers of the object(s) we want to change etc.

Based on the identifier of the object(s) we can easily fetch them from DB which resolves the problem with desynchronizing 
because we always make queries to DB for the newest state of an object.

* good, because there is no problem with desynchronizing data while async communication
* good, because we can easily integrate with external systems
* good, because it's more consistent with CQRS pattern
* bad, because we need to always make queries to DB to get objects we want to modify (but we are caching queries from 
DB when we are using doctrine as our ORM)

## Decision outcome

Chosen option: **"Passing only raw data or identifiers of the object(s) we want to change etc."**

This option gives us plenty of advantages with only a small problem with always making queries when we want to work 
with DB objects.
