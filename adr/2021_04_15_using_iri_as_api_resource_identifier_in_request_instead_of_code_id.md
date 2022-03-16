# Using IRI as API resource identifier in requests instead of code/id

* Status: accepted
* Date: 2021-04-15

## Context and Problem Statement

API Platform recommends using IRI as an identifier. That identifier gives more clarity than an id because it contains more information - a full endpoint path to the resource and its unique identifier. On resources, API Platform handles IRI out of the box.
While we were designing Sylius new API, we have decided to use commands in many endpoints.
In some cases, this solution is more flexible than the default creation approach (as we have control over what we are processing), but it doesn't handle the transformation of IRI to proper `code`/`id`.
In the past, we have been using `code`/`id` instead of IRI, then we have been using both approaches.
Now we are trying to unify the new API and replace codes and ids with IRI everywhere.
The main challenge is the usage of IRI in requests, where we want to have IRI in the request but `id`/`code`in its commands and command handlers.

## Considered Options

### Using `id`/`code` instead of IRI

Using `code`/`id` instead of IRI is easier to implement, but it is not consistent with API Platform and its default behaviour.

* Good, because it is easier to implement
* Bad, because it is not consistent with others endpoints

### Handling and transforming IRI to `id`/`code`

For handling IRI we created `Sylius\Bundle\ApiBundle\Serializer\CommandFieldItemIriToIdentifierDenormalizer` and
`Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMap`. `CommandFieldItemIriToIdentifierDenormalizer`
transforms and denormalizes our command using `CommandItemIriArgumentToIdentifierMap` as a bag with definition for
supported denormalize commands. All you have to do for adding command support is add command FQCN as a key
and field name as a field that will be transformed from IRI to `code`/`id`. All definition are registered in
`Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMap` service:

````
        <service id="Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMap">
            <argument type="collection">
                <argument key="Sylius\Bundle\ApiBundle\Command\AddProductReview">product</argument>
                <argument key="Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod">paymentMethod</argument>
                <argument key="Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod">paymentMethod</argument>
                <argument key="NewCommandFQCN">NewCommandFieldName</argument>
            </argument>
        </service>
````

* Good, because it unifies the API structure
* Good, because it makes the API easier to use
* Bad, because it imposes a new abstraction layer for commands

## Decision Outcome

Chosen option: "Handling and transforming IRI to `id`/`code`". Request that is based on command and needed information like `code`/`id` should get it as IRI
