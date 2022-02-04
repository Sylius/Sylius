@checkout
Feature: Checking out as guest with existing email, while email owner starts a new cart session
    In order to make the checkout cart available only for user who owns the cart
    As a Logged in user
    I want to be able to checkout with my previous cart when someone used my email in checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$20.00"
        And the store has a product "Kotlin T-Shirt" priced at "$30.00"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a user "robb@stark.com" identified by "KingInTheNorth"
        And I am logged in as "robb@stark.com"

    @ui
    Scenario: Preventing anonymous user claiming logged in user's cart
        Given I have product "PHP T-Shirt" in the cart
        When I log out
        And I add products "PHP T-Shirt" and "Kotlin T-Shirt" to the cart
        And I complete addressing step with email "robb@stark.com" and "United States" based billing address
        And Logged in user with email "robb@stark.com" starts a new session
        And He views cart
        Then there should be one item in his cart
        And his cart's total should be "$20.00"
