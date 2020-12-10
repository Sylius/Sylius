@applying_shipping_method_rules
Feature: Viewing available shipping methods based on order total
    In order to only see applicable shipping methods
    As a Customer
    I want to see the shipping methods that are available to my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Cheap Jacket" priced at "$20.00"
        And the store has a product "Expensive Jacket" priced at "$200.00"
        And the store has "DHL" shipping method with "$20" fee
        And the store has "Ship with us, ship with pride" shipping method with "$200" fee
        And this shipping method is only available for orders over or equal to "$100"
        And the store has "We delivery cheap goodz" shipping method with "$2" fee
        And this shipping method is only available for orders under or equal to "$50"
        And I am a logged in customer

    @ui @api
    Scenario: Seeing shipping methods that handle expensive goods
        Given I have product "Expensive Jacket" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "Ship with us, ship with pride" shipping method
        And I should not see "We delivery cheap goodz" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle cheap goods
        Given I have product "Cheap Jacket" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "We delivery cheap goodz" shipping method
        And I should not see "Ship with us, ship with pride" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle all goods
        Given I have product "Cheap Jacket" in the cart
        And I add 2 of them to my cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should not see "We delivery cheap goodz" shipping method
        And I should not see "Ship with us, ship with pride" shipping method
