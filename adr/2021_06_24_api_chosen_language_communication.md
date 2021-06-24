# API - Chosen language communication

* Status: proposed
* Date: 2021-06-24

## Context and Problem Statement

There is a need to provide unified, coherent way of communicating chosen language of the customer for our API 

## Considered Options

### Usage of Accept-Language

There is dedicated HTTP Header for language information, usually filled by browser automatically. This approach follows HTTP
content negotiation paradigm. The resource is same, but depending on the locale its representation may differ.

* Good, because it is compliant with REST.
* Good, because it uses already defined and existing specification.
* Bad, because there is some dispute if usage of the header in this context is proper and/or enough.

### Usage of language prefix

Approach currently used by default in every Sylius instantiation. Nonetheless, several times issues were raised, how to turn
this feature off or why it was decided for this approach. Still, API is always behind some scaffolding from the user perspective,
so this is viable option. This approach makes it even harder to maintain consistent API and proper URL, as every route should
be prefixed with language.

* Good, because maintain currently existing behaviour in HTML based implementation
* Good, because it is easy to use and define the chosen locale directly in URL.
* Bad, because it violates REST paradigm.

### Usage of language query parameter

Quite common approach, where each url may be followed with the lang/language/locale query parameter.

* Good, because it is easy to use and define the chosen locale directly in URL.
* Bad, because it violates REST paradigm. 

## Decision Outcome

We should be using Accept-Language header in the initial implementation. With time, we may consider adding additional LocaleContext
to provide language query parameter as a second option.
