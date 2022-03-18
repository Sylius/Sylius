# API - Translations

* Status: accepted
* Date: 2020-03-03

In power from version: 1.8.0

Related PRs: #11136, #11144, #11157

## Description

### Problem to solve

Provide unified way to manage translations for translatable entities in API. 

### Possible solutions

The translations from collection could be embedded as objects within a `ProductOption`/`ProductOptionValue` resource
**or** provided by [IRIs](https://en.wikipedia.org/wiki/Internationalized_Resource_Identifier)

### Decision and reasoning

Translations should always be embedded as the collection of objects. They're irrelevant outside of the main object and
do not provide any value alone.
