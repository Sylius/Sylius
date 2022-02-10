@checkout
Feature: Preventing claiming cart of logged in user
    In order to make the checkout cart available only for user who owns the cart
    As a Customer
    I want to be able to checkout with my previous cart when someone used my email in checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$20.00"
        And the store has a product "Kotlin T-Shirt" priced at "$30.00"
        And the store has a product "Symfony T-Shirt" priced at "$100.00"
        And the store has a product "Sylius T-Shirt" priced at "$150.00"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a user "robb@stark.com" identified by "KingInTheNorth"
        And I am logged in as "robb@stark.com"

    @ui
    Scenario: Preventing anonymous user claiming logged in user's cart
        Given I have product "PHP T-Shirt" in the cart
        When an anonymous user in another browser adds products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And he completes addressing step with email "robb@stark.com" and "United States" based billing address
        And I view cart on my browser
        Then there should be one item in my cart
        And my cart's total should be "$20.00"
