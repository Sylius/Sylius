# Remove flush from handlers

* Status: accepted
* Date: 2022-03-24

## Context and Problem Statement

When working with entities and persisting the current state to DB one has to call a flush method on entity manager. 
By default, it starts and commits transactions to used DB. The other possibility is to start the transaction manually, 
which will suspend the auto-commit feature of Doctrine.

## Decision Drivers

* avoid inconsistent data in DB
* flexibility to rollback changes
* provide an easy way to interface with object state before committing transaction

## Considered Options

### Flushing in handlers

Using flush() in command handlers can cause problems with inconsistent DB states if the end-user decides to adjust 
models definitions. Implicitly flushing will commit all the changes queued up so far, which may lead to DB error or 
committing not consistent data. What is more, we would need an additional feature that would allow adjusting the entity's 
state before the transaction will close.

* Bad, because implicitly flushing will commit all the changes queued up so far
* Bad, because we would need an additional feature

### Flushing outside of handlers with the manually triggered transaction

Manual setting up of transactions around handlers clearly defines their boundaries. Even tho this may still lead to DB 
error or committing not consistent data, setting boundaries outside of our code improves DX greatly, as an adjustment 
on objects may be made with decorator pattern outside of predefined handler.

* Good, because it improves DX
* Good, because transactions around handlers clearly define their boundaries
* Good, because we have flexibility to rollback changes
* Bad, because it may still lead to errors or committing not consistent data

## Decision Outcome

Chosen option: **"Flushing outside of handlers with the manually triggered transaction"**

It is the most straightforward solution which will suspend the auto-commit feature of Doctrine and gives us more control
and flexibility when working with data.

## References <!-- optional -->

* [Transactions and Concurrency](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/transactions-and-concurrency.html)
