@shopping_cart
Feature: Viewing a cart summary with correct default shipping method based on variant applied
    In order to see details about my order
    As a visitor
    I want to be able to see my cart summary with the correct shipping method based on variants

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over-sized" shipping category
        And the store has "Over-sized shipment method" shipping method with "35.00" fee
        And this shipping method requires at least one unit matches to "Over-sized" shipping category
        And the store has a product "Star Trek Ship" priced at "$79.99"
        And this product belongs to "Over-sized" shipping category
        And the store has "Small-sized" shipping category
        And the store has "Small-sized shipment method" shipping method with "5.00" fee
        And this shipping method requires at least one unit matches to "Small-sized" shipping category
        And the store has a product "Star Trek Table Linen" priced at "$19.99"
        And this product belongs to "Small-sized" shipping category
        And the store has "Free" shipping category
        And the store has "Free" shipping method with "0.00" fee
        And this shipping method requires that all units match to "Free" shipping category
        And the store has a product "T-shirt banana"
        And this product has option "Size" with values "S" and "M"
        And this product is available in "S" size priced at "$12.54"
        And this product is available in "M" size priced at "$12.30"
        And this product belongs to "Free" shipping category

    @ui
    Scenario:
        Given I added product "Star Trek Table Linen" to the cart
        When I see the summary of my cart
        Then my cart shipping total should be "5.00"

    @ui
    Scenario:
        Given I have "S" variant of product "T-shirt banana" in the cart
        And I added product "Star Trek Ship" to the cart
        When I see the summary of my cart
        Then my cart shipping total should be "35.00"

    @ui
    Scenario:
        Given I have "S" variant of product "T-shirt banana" in the cart
        When I see the summary of my cart
        Then my cart shipping total should be "0.00"
