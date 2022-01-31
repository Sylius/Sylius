# Unified API prefix

* Status: accepted
* Date: 2021-02-05

## Context and Problem Statement

Initial implementation of Unified API used `/new-api` prefix, to aggregate all following endpoints. This prefix does not 
clearly state the version of it and is not future-proof. At some moment of time our "new api" can become "old api". We should 
have clear guidance how to version our APIs.

## Considered Options

### Using URL based versioning

We could change `/new-api` into `/api/v2`.

* Good, because it follows an already established pattern. Our old Admin API had `/api/v1` prefix.
* Good, because it's easy to understand API precedence.
* Good, because it's easy to use. Links are working out-of-the-box as expected.

* Bad, because it makes it hard for the clients to slowly migrate some subset of API to the new version.
* From the REST point of view, the version in URL should not influence the presentation of resource.

### Using the Accept header with an additional vendor information 

We could change `/new-api` into `/api` and require presence of following header: `Accept: application/vnd.sylius.v1+json`.

* Good, because it may be considered as a best practice.
* Good, because `Accept` header was meant for content negotiation.

* Bad, because it breaks an already established pattern. Our old Admin API had `/api/v1` prefix. 
* Bad, because it's a bit harder to execute in a RAW form.

### Using a custom header 

We could change `/new-api` into `/api` and require presence of following header: `X-Sylius-API-Version: 1`.

* Bad, because it does not provide any major benefit over usage of `Accept` header

## Additional context

At the moment of writing of this document, API Platform does not have a clear answer how to resolve API versioning. Recommendation
variate between usage of serialization groups to choose proper serialization delivered to app in whatever way to creation
of separate app all together.

## Decision Outcome

As the underlaying technology, structure and content have changed significantly and taking into account easiness of first solution
the decision is to go with the `/api/v2` endpoint path. In the future it does not block us from the usage of the `Accept`
header in addition to this path, however it may be misleading for consumers. 

#### References:

- https://github.com/api-platform/api-platform/issues/290
- https://github.com/api-platform/core/issues/972
- https://stackoverflow.com/questions/389169/best-practices-for-api-versioning
- https://www.troyhunt.com/your-api-versioning-is-wrong-which-is/
- https://github.com/api-platform/core/issues/45
- https://github.com/api-platform/docs/issues/451
- https://github.com/api-platform/docs/pull/452
- https://api-platform.com/docs/core/content-negotiation/#supporting-custom-formats
