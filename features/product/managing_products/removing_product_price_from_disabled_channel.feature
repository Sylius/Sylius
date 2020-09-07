@managing_products
Feature: Removing a product's price from disabled channel
    In order to have only valid prices on enabled channels
    As an Administrator
    I want to be able to remove price from disabled channels

    Background:
        Given the store has currency "USD"
        And the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And the store has a product "Dice Brewing" priced at "$10.00" in "Web-US" channel
        And this product is also priced at "£5.00" in "Web-GB" channel
        And this product is disabled in "Web-GB" channel
        And I am logged in as an administrator

    @ui
    Scenario: Removing a product's price from disabled channel
        When I want to modify the "Dice Brewing" product
        And I remove its price for "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
