@managing_orders
Feature: Limiting the number of orders
    In order to display a specific number of orders
    As an Administrator
    I want to be able to limit the number of orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Blue ElePHPant"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer "john@doe.com" that placed an order "#00000022"
        And the customer bought a single "Blue ElePHPant"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And this customer placed another order "#00000023"
        And the customer bought a single "Blue ElePHPant"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @api
    Scenario: Limiting the number of orders
        When I browse orders
        And I limit number of items to 1
        Then I should see a single order in the list
