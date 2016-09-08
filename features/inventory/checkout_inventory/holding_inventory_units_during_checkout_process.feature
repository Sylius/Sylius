@checkout_inventory @managing_inventory
Feature: Holding inventory units during checkout
    In order to be sure that products I buy were not bought by another customer
    As a Customer
    I want to buy selected products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt" priced at "€12.54"
        And the store also has a product "2Pac T-Shirt" priced at "€13.24"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And "2Pac T-Shirt" product is also tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And there are 10 units of product "2Pac T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline
        And there is an administrator "sylius@example.com" identified by "sylius"
        And there is a customer account "customer@example.com" identified by "sylius"
        And I am a logged in customer

    @todo
    Scenario: Holding inventory units
        Given I have added 3 products "Iron Maiden T-Shirt" to the cart
        When I proceed selecting "Offline" payment method
        Then the administrator should know that 3 units of this product is hold

    @todo
    Scenario: Prevent buying hold units by another customer
        Given I have added 5 products "Iron Maiden T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        When the customer "customer@example.com" wants to buy 3 units of this product
        Then this customer should be notified that this product does not have sufficient stock
