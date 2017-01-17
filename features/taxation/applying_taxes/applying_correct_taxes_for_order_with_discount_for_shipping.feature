@applying_taxes
Feature: Apply correct taxes for an order with a discount for a shipping
    In order to pay proper amount when buying goods
    As a Visitor
    I want to have correct taxes applied to my order with a discount

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "Low VAT" tax rate of 10% for "Shipping" within the "US" zone
        And the store has a product "Symfony Mug" priced at "$10.00"
        And there is a promotion "Holiday promotion"
        And the promotion gives "10%" discount on shipping to every order
        And I am a logged in customer

    @ui
    Scenario: Properly rounded up tax
        Given the store has "DHL" shipping method with "$51.06" fee
        And shipping method "DHL" belongs to "Shipping" tax category
        And I have product "Symfony Mug" in the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$60.55"
        And my cart taxes should be "$4.60"

    @ui
    Scenario: Properly rounded down tax
        Given the store has "DHL" shipping method with "$51.04" fee
        And shipping method "DHL" belongs to "Shipping" tax category
        And I have product "Symfony Mug" in the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$60.53"
        And my cart taxes should be "$4.59"

    @ui
    Scenario: Properly calculated taxes when item belongs to different tax category
        Given the store has "Standard VAT" tax rate of 23% for "Mugs" within the "US" zone
        And the store has a product "Sonata Mug" priced at "$10.00"
        And it belongs to "Mugs" tax category
        And the store has "DHL" shipping method with "$51.04" fee
        And shipping method "DHL" belongs to "Shipping" tax category
        And I have product "Sonata Mug" in the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$62.83"
        And my cart taxes should be "$6.89"
