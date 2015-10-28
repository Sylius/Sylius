@integration
Feature: Integration
    In order to have stable application
    As a developer
    I need to be able to check existence of services

    Scenario Outline: Existence of the Services in CoreBundle
         When I get the service "<serviceName>" from the Kernel container
         Then I should get an instance of "<serviceClass>"

    Examples:
    | serviceName            | serviceClass                                          |
    | sylius.route_provider  | Sylius\Bundle\CoreBundle\Routing\RouteProvider        |
    | sylius.route_generator | Sylius\Bundle\CoreBundle\Routing\SyliusAwareGenerator |
#        ... to be continued

    Scenario Outline: Existence of the dispatchers in CoreBundle
        When I dispatch an event with "<eventName>"
        Then I should see "<eventDispatcher>" with "<methodName>" called

    Examples:
    | eventName                           | eventDispatcher                                                | methodName           |
    | sylius.cart.initialize              | Sylius\Bundle\CoreBundle\EventListener\OrderCurrencyListener   | processOrderCurrency |
    | sylius.user.security.implicit_login | Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener      | blame                |
    | sylius.user.security.implicit_login | Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber | onImplicitLogin      |
#        ... to be continued
