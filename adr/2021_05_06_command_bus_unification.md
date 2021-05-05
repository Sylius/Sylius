# Unification of buses in sylius

* Status: accepted
* Date: 2021-05-06

## Context and Problem Statement

We had 7 different names of buses across all sylius products, we decided to unify them.

## Considered Options

### Leaving different buses as it was till now

* Good, because it doesnt require much work
* Bad, because we need to keep track of all available buses

### Unifying buses across all products

* Good, because we will have only few buses to keep track of
* Bad, because potential bc break

## Decision Outcome

Chosen option: Unifying buses across all products, it allows us stick to one bus naming style
