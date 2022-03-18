# Using Doctrine Collection extension for getting available locales in active channel for shop user

* Status: accepted
* Date: 2022-03-15

## Context and Problem Statement
Customer should have access only to locales available in their channel

## Considered Options

### Using Doctrine Collection extension

* Good, because it is consistent with the actual approach of modifying responses content
* Good, because it works with the rest of API extensions like pagination
* Bad, because locales don't have relation to channel, so using Doctrine Collection extension is complicated

### Using Data Provider

* Good, because it is easy to implement 
* Bad, because using data providers omits extra Doctrine extensions like pagination

## Decision Outcome

Chosen option: **"Using Doctrine Collection extension"**

This option is consistent with current approach and does not omit Doctrine extensions like pagination.

## References

* [Original ADR for this problem](2021_07_05_api_providing_locales_available_in_active_channel.md)
* [The implementation of changing the approach](https://github.com/Sylius/Sylius/pull/13333)
