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
        And I am a logged in customer

    @ui
    Scenario: Adding proper shipping fee
        Given I have product "PHP T-Shirt" in the cart
        When I proceed selecting "DHL" shipping method
        Then my cart total should be "$110.00"
        And my cart shipping total should be "$10.00"

    @ui
    Scenario: Changing shipping fee after shipping method change
        Given I have product "PHP T-Shirt" in the cart
        And I chose "DHL" shipping method
        When I change shipping method to "FedEx"
        Then my cart total should be "$130.00"
        And my cart shipping total should be "$30.00"
