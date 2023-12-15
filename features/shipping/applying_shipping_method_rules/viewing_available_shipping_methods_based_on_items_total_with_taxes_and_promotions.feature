@applying_shipping_method_rules
Feature: Viewing available shipping methods based on items total
    In order to only see applicable shipping methods
    As a Customer
    I want to see the shipping methods that are available to my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 20% for "Clothes" within the "US" zone
        And the store has "Shipping VAT" tax rate of 10% for "Shipping Services" within the "US" zone
        And the store has a product "Cheap Jacket" priced at "$20.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Expensive Jacket" priced at "$47.00"
        And it belongs to "Clothes" tax category
        And the store has "Above $50" shipping method with "$1.00" fee within the "US" zone
        And shipping method "Above $50" belongs to "Shipping Services" tax category
        And this shipping method is only available for orders over or equal to "$50.00"
        And the store has "Below $29.99" shipping method with "$20.00" fee within the "US" zone
        And shipping method "Below $29.99" belongs to "Shipping Services" tax category
        And this shipping method is only available for orders under or equal to "$29.99"
        And the store has "DHL" shipping method with "$20.00" fee
        And there is a promotion "50% shipping discount"
        And it gives "50%" discount on shipping to every order
        And there is a promotion "Expensive promotion"
        And it gives "$5.00" off on a "Expensive Jacket" product
        And I am a logged in customer

    @ui @api
    Scenario: Seeing shipping methods that handle expensive goods
        Given I have product "Expensive Jacket" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "Above $50" shipping method
        And I should not see "Below $29.99" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle cheap goods
        Given I have product "Cheap Jacket" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should see "Below $29.99" shipping method
        And I should not see "Above $50" shipping method

    @ui @api
    Scenario: Seeing shipping methods that handle all goods
        Given I have 2 products "Cheap Jacket" in the cart
        When I specified the billing address
        Then I should be on the checkout shipping step
        And I should see "DHL" shipping method
        And I should not see "Above $50" shipping method
        And I should not see "Below $29.99" shipping method
