@managing_inventory
Feature: Validation of decreasing inventory below on hold validation
    In order to be prevented form setting incorrect inventory amount
    As an Administrator
    I want to be prevented from decreasing inventory below on hold's value

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And "Wyborowa Vodka" product is tracked by the inventory
        And there are 5 units of "Wyborowa Vodka Exquisite" variant of product "Wyborowa Vodka" available in the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000023"
        And the customer bought 4 units of "Wyborowa Vodka Exquisite" variant of product "Wyborowa Vodka"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Decreasing inventory when order was placed
        Given I want to modify the "Wyborowa Vodka Exquisite" product variant
        When I change its quantity of inventory to 2
        And I save my changes
        Then I should be notified that on hand quantity must be greater than the number of on hold units
        And this variant should have a 5 item currently in stock

    @ui
    Scenario: Decreasing inventory when order was cancelled
        Given the order "#00000023" was cancelled
        And I want to modify the "Wyborowa Vodka Exquisite" product variant
        When I change its quantity of inventory to 2
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this variant should have a 2 item currently in stock

    @ui
    Scenario: Decreasing inventory when order was paid
        Given the order "#00000023" is already paid
        And I want to modify the "Wyborowa Vodka Exquisite" product variant
        When I change its quantity of inventory to 2
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this variant should have a 2 item currently in stock
