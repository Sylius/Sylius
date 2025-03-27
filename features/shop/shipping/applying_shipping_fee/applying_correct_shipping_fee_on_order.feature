@applying_shipping_fee
Feature: Apply correct shipping fee on order
    In order to decide on amount paid for shipment
    As a Customer
    I want to have shipping fee applied based on chosen shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store has "FedEx" shipping method with "$30.00" fee
        And the store has "UPS" shipping method with "$5.00" fee per unit for "United States" channel
        And I am a logged in customer

    @api @ui
    Scenario: Adding proper shipping fee
        Given I have product "PHP T-Shirt" in the cart
        And I addressed the cart
        And I completed the shipping step with "DHL" shipping method
        When I check details of my cart
        Then my cart total should be "$110.00"
        And my cart shipping total should be "$10.00"

    @api @ui
    Scenario: Changing shipping fee after shipping method change
        Given I have product "PHP T-Shirt" in the cart
        And I addressed the cart
        And I completed the shipping step with "DHL" shipping method
        When I decide to change order shipping method
        And I change shipping method to "FedEx"
        And I complete the shipping step
        Then my cart total should be "$130.00"
        And my cart shipping total should be "$30.00"

    @api @ui @javascript
    Scenario: Changing per unit shipping fee after decreasing quantity of item
        Given I have 2 products "PHP T-Shirt" in the cart
        And I addressed the cart
        And I completed the shipping step with "UPS" shipping method
        When I change "PHP T-Shirt" quantity to 1
        Then my cart total should be "$105.00"
        And my cart shipping total should be "$5.00"
