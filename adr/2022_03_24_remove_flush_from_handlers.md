# Remove flush from handlers

* Status: accepted
* Date: 2022-03-24

## Context and Problem Statement

Using flush() in command handlers can cause problems with inconsistent DB state. Doctrine always uses transactions but on
flush() Doctrine will execute all the changes that have been tracked so far. Implicitly flushing will commit all the 
changes queued up so far. Not just the entity you just persisted. This is also one of the reasons why it is dangerous to
just call flush.

## Decision Drivers

* avoid inconsistent data in DB
* flexibility to rollback changes

## Decision Outcome

We decided to remove all flushes from command handlers and let DoctrineTransactionMiddleware do it for us because
it gives us ability to avoid an inconsistent data and if something fails we have flexibility to rollback changes.

## References <!-- optional -->

* [Transactions and Concurrency](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/transactions-and-concurrency.html)
