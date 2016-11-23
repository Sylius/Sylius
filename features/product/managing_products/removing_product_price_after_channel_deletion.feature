@managing_products
Feature: Removing a product's price after channel deletion
    In order to have product's prices specified only for existing channels
    As an Administrator
    I want to have product's price removed after corresponding channel deletion
    
    Background:
        Given the store has currency "USD"
        And the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And the store has a product "Dice Brewing" priced at "$10.00" in "Web-US" channel
        And this product is also priced at "£5.00" in "Web-GB" channel
        And I am logged in as an administrator

    @ui
    Scenario: Removing a product's price after corresponding channel deletion
        When channel "Web-GB" has been deleted
        Then product "Dice Brewing" should be priced at $10.00 for channel "Web-US"
        And this product should no longer have price for channel "Web-GB"
