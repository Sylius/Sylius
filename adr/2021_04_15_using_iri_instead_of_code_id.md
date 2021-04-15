# Using iri instead of code/id in requests

* Status: proposed
* Date: 2021-04-15

## Context and Problem Statement

ApiPlatform provides and recommends using IRI as identifier. That identifier is more clearly than classic id and 
contain more information. On resources, ApiPlatform handles IRI out of the box.
During creation our new API in many places we decided to use commands while creating our endpoints. For certain cases
this solution is more flexible than classic approach, but it doesn't handle transformation IRI to proper `code`/`id`.
In the past we decided to use `code`/`id` instead of IRI. Now we try to unify API and replace this approach for solution with using IRI.
Main problem is sent request with field as IRI and creation command without IRI, but with `id`/`code`. 
It's needed for proper handling behaviour in command handler.

## Considered Options

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
                <argument key="NewCommandFQCN">NewCommandFieldName</argument><-- Example -->
            </argument>
        </service>
````

* Good, because it unifies API structure
* Good, because it causes API easier to use
* Good, because it allows creating request with field on command as IRI
* Good, because it leaves command field without IRI but with `id`/`code`
* Bad, because it imposes new abstraction layer for commands

## Decision Outcome

Request that is based on command and needed information like `code`/`id` should get it as IRI
