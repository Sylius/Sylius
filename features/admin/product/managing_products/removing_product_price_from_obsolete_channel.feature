@managing_products
Feature: Removing a product's price from the channel where it is not available in
    In order to have only valid prices on channels where product is available in
    As an Administrator
    I want to be able to remove price from obsolete channels

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "USD" currency
        And the store has a product "Dice Brewing" priced at "$10.00" in "Web-US" channel
        And this product is also priced at "£5.00" in "Web-GB" channel
        And this product is originally priced at "£70.00" in "Web-GB" channel
        And this product is disabled in "Web-GB" channel
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Removing a product's price from disabled channel
        Given the channel "Web-GB" has been disabled
        When I want to modify the "Dice Brewing" product
        And I remove its price from "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
        But I should have original price equal to "£70.00" in "Web-GB" channel

    @ui @no-api
    Scenario: Removing a product's price from obsolete channel
        Given this product is disabled in "Web-GB" channel
        When I want to modify the "Dice Brewing" product
        And I remove its price from "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
        But I should have original price equal to "£70.00" in "Web-GB" channel
