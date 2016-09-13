@checkout_inventory
Feature: Holding inventory units during checkout
    In order to be sure that products I buy were not bought by another customer
    As an Administrator
    I want to buy selected products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a user "sylius@example.com" identified by "sylius"
        And I am logged in as an administrator

    @ui
    Scenario: Holding inventory units
        Given this user has added 3 products "Iron Maiden T-Shirt" to the cart
        And this user bought those products
        When I view all variants of the product "Iron Maiden T-Shirt"
        Then I should know that 3 units of this product is hold

    @ui
    Scenario: Release hold units after order has been paid
        Given this user has added 3 products "Iron Maiden T-Shirt" to the cart
        And this user bought this product
        When I view the summary of this order made by "sylius@example.com"
        And I mark the order of "sylius@example.com" as a paid
        And I view all variants of the product "Iron Maiden T-Shirt"
        Then I should not know about on hold quantity of this product
