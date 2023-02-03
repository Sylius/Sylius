# API - Customization internationalization

* Status: proposed
* Date: 2021-06-24

## Context and Problem Statement

A big chunk of resources available in Sylius have some part locale aware. Product descriptions, names of payment or shipping methods
should be translated to the customer to provide the best user experience. Right now most of the resources are returned with 
all available translations in the store and proper localization of content is left to the frontend developers. What is more,
this solution is not consistent, as some endpoints already embeds some part of translatables in them. We should provide clear,
easy to use and consistent way of handling customer locales. 

## Considered Options

### Returning all possible translations to the frontend

We can update all not coherent at the moment resources to always returning all possible translations and leave it to the fronted to render proper one.

* Good, because it is simplest and fastest solution at the moment of writing this document.
* Good, because it leaves decision of proper content localization to the frontend developer. This guarantees flexibility.
* Bad, because of data over-fetching. Most of the locales in the most to the cases won't be needed.
* Bad, because it leaves decision of proper content localization to the frontend developer. Additional work is required on the frontend
  App in order to provide localization functionality.

### Taking advantage of JSON-LD specification for strings internationalization

As researched by @pamil, we can take advantage of JSON-LD processor, return data according to its specification and leave 
proper data structure to the JSON-LD processor.

* Good, because it follows some external standard. This may allow us to take an advantage of the standard ecosystem.
* Good, because it solves over-fetching problem, yet leaves the flexibility to the developer if they decide to fetch more than one locale in a single request.
* Good, because it is suggested as a good direction for content internationalization by the API Platform core team. 
* Bad, because we are bounded to only one standard. It may not solve internationalization issues with other formats than JSON-LD.
* Bad, because it is not part of API Platform yet and significant effort would need to be done in order to provide proper solution.

Ref.
* https://github.com/Sylius/Sylius/issues/11412
* https://github.com/api-platform/core/issues/127

### Flatting down translatable parts of resources to the structure of the main resource

This approach goes the most side to side with the default Sylius architecture. This is the way, how we treat Sylius resources 
internally for most of the cases. It is supported out-of-the-box and can be easily achieved. By default all resources are 
served in a default locale of a channel (or Administrator in terms of admin panel). This may fallback to app wise default locale.

* Good, because it follows our current behaviour.
* Good, because it solves over-fetching problem.
* Good, because it is straight-forward to implement.
* Bad, because it does not allow fetching more than one locale.
* Bad, because it will become our internal standard only.

## Decision Outcome

The first approach should be used in admin section of API, while the third one should be used in shop section. This way, we
will receive best of both worlds with the minimal effort. It is worth mentioning that Sylius have two distinguish entry points
to API - admin and shop. They are concerned about different things and resolves different problems. Unified view of a product
in the shop will make it easy and straightforward to build product pages. At the same time all translation approach should be used in
admin to provide full control over "translation" resource - as we should treat them. Therefore, every resource translation
should be linked to the main resource through IRI. Each of these translation resource may be a subject of all CRUD operations
accessible throughout HTTP Verbs.
