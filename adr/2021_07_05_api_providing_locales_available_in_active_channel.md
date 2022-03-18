# Using data provider for getting available locales in active channel for shop user

* Status: rejected
* Date: 2021-07-05

## Context and Problem Statement
Customer should have access only to locales available in their channel

## Considered Options

### Using Doctrine Collection extension

* Good, because it is consistent with the actual approach to modifying responses content
* Good, because it works with the rest of API extensions like pagination
* Bad, because locales don't have relation to channel, so using Doctrine Collection extension is extremely hard.

### Using Data Provider

* Good, because we already have this approach for older resources
* Good, because it is easy to implement 
* Bad, because using data providers omits extra Doctrine extensions like pagination

## Decision Outcome

Chosen option: Using Data Provider
Shops shouldn't have many locales for each channel, so lack of a pagination is smaller problem than creating overcomplicated 
query in Doctrine Collection extension
