# Handling Non-CRUD operations in API

* Status: accepted
* Date: 2020-08-31

## Context and Problem Statement

Handling non-CRUD operation over REST API is not trivial. Once operations are beyond Create(`POST`/`PUT` HTTP methods), 
Read(`GET` HTTP method), Update(`PUT`/`PATCH` HTTP methods), Delete(`DELETE` HTTP method) there is no clear recommendation 
how to map others actions. These actions include, but are not limited to, changes of states (described in the form of 
workflow in a state machine) or command execution.

## Decision Drivers

* Solution should not be limited by its convention. We may need to support two different actions of the same name, 
but with different business logic. E.g., one object may have two transitions with the same name but described by 
two different state machines.
* Solution should allow for an easy understanding of what is expected behavior of its execution.
* Solution should provide easy to implement a way of exposing the next possible actions, according to HATEOAS paradigm.
* Solution should provide a clean way of adding additional fields during the execution of requested operations.

## Considered Options

All options are presented on the example of making a `ship` operation over the shipment, that is ready to be shipped.
 
Expectations: being able to call `ship` transition on the `Shipment` with the XYZ tracking code. 

### Enrich resource data with possible operations

It is possible to add additional fields or reuse current ones to accept operations and leverage HTTP Verbs. In 
this solution we can use regular `Update` request to execute additional logic:

PUT `/api/orders/1/shipment/1`

```json
{
  "status": "ship",
  "tracking_code": "XYZ"
}
```

This solution works for all standards supported by ApiPlatform(JSON-LD, JSON HAL, GraphQL).

* Good, because it does not introduce verbs into URL (which may be considered as a bad practice for REST API)
* Good, because it does not introduce any new endpoints.
* Bad, because calling `ship` on `status` may be misleading, as a result, may be different(In presented example command 
`"status": "ship"` results with `"status": "shipped"`).
* Bad, because it is not clear how to expose possible transitions. Should a separate endpoint be created, or data should
 be further enriched with the `possibleTransitions` field? It is not clear how non-state machine operations should be 
 handled as well. 
* Bad, because it uses `PUT` operation for action, which is nonidempotent, which may mislead end clients.
* Bad, because it allows for using only one type of transition per field.

### Defining custom operations in the style of the command pattern

We may trace operations as separate resources. We may operate with these operations using regular HTTP Verbs. All these 
operations should be named as nouns. 

POST `/api/orders/1/shipment/1/ship-attempts`

```json
{
  "tracking_code": "XYZ"
}
```

* Good, because it does not introduce verbs into URL (which may be considered as a bad practice for REST API)
* Good, because there may be a straight forward way to expose possible transitions (GET `/api/orders/1/shipment/1/ship-attempts`)
* Good, because it provides a clear solution for all custom endpoints. 
* Good, because it uses `POST` operation for action, which is not idempotent. 
* Bad, because state changes are not traceable resources in Sylius. They do not have unique identifiers to check their 
status, nor are they possible for retries.
* Bad, because there is no clear solution on how to declare links in JSON-LD format.

### Taking advantage of the `Controller` REST archetype

This approach was described in the REST API Design Rulebook, which states 4 archetypes: `Document`, `Collection`, `Store`,
and `Controller`. `Controller` archetype allows for custom operations over the resource with the custom verb as the last 
part of the URL. However, these custom actions cannot duplicate the logic of any HTTP verbs.

POST `/api/orders/1/shipment/1/ship`

```json
{
  "tracking_code": "XYZ"
}
```

* Good, because it is already supported by Sylius Admin REST API - which reduces the number of possible problems during 
migration.
* Good, because it is straight-forward and easy to comprehend.
* Good, because it provides a clear solution for all custom solutions. 
* Good, because it uses `POST` operation for action, which is not idempotent. 
* Bad, because there is no clear solution on how to declare links in JSON-LD format.
* Bad, because usage of verbs in URL may be considered as a bad practice. 

### Google recommendation for custom operations

Google is taking an approach of suffixing its custom endpoints with `:operation`.

POST `/api/orders/1/shipment/1:ship`

```json
{
  "tracking_code": "XYZ"
}
```

* Good, because it is straight-forward and easy to comprehend.
* Good, because it provides a clear solution for all custom solutions. 
* Good, because it uses `POST` operation for action, which is nonidempotent. 
* Bad, because it seems to be unnatural for standard REST endpoints.
* Bad, because it can be hard to achieve with the API Platform.

## Decision Outcome

The "Taking advantage of the `Controller` REST archetype" should be considered as a recommended solution. All Sylius 
users are already familiar with it, and it is easy to understand expected behavior. Linked data references should provide
the discoverability of the new endpoints. The possible operation may be sent in the `Link` header
or new schema should be introduced for the JSON-LD structure. 

Option 2: "Defining custom operations in the style of command pattern" may be useful once async data processing is 
delivered with vanilla Sylius installation. 

## References

* [Json-LD & Hydra links to custom operations](https://stackoverflow.com/questions/41125810/json-ld-with-hydra-how-to-define-a-custom-operation-and-specify-its-url) 
* [Hydra links representation](https://www.hydra-cg.com/spec/latest/core/#adding-affordances-to-representations)
* [Command pattern in REST](https://allegro-restapi-guideline.readthedocs.io/en/latest/CommandPattern/)
* Masse, M., REST API design rulebook. 2011:" O'Reilly Media. Chapter 2 - Identifier Design with URIs.
* [Google custom endpoint design guide](https://cloud.google.com/apis/design/custom_methods)
* [Representing State in REST and GraphQL](https://phil.tech/api/2017/06/19/representing-state-in-rest-and-graphql/#hateoas-i-call-on-thee)
