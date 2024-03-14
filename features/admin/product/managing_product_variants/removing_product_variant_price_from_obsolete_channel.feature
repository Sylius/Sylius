@managing_product_variants
Feature: Removing a product variant's price from obsolete channel
    In order to have only valid prices on channels where product is available in
    As an Administrator
    I want to be able to remove price from obsolete channels

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "USD" currency
        And the store has a "PHP Mug" configurable product
        And this product has "Medium PHP Mug" variant priced at "$20.00" in "Web-US" channel
        And "Medium PHP Mug" variant priced at "£25.00" in "Web-GB" channel
        And "Medium PHP Mug" variant is originally priced at "£50.00" in "Web-GB" channel
        And this product is disabled in "Web-GB" channel
        And I am logged in as an administrator

    @api @ui
    Scenario: Removing a product variant's price
        When I want to modify the "Medium PHP Mug" product variant
        And I remove its price from "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
        But I should have original price equal to "£50.00" in "Web-GB" channel

    @api @ui
    Scenario: Removing a product variant's price from disabled channel
        Given the channel "Web-GB" has been disabled
        When I want to modify the "Medium PHP Mug" product variant
        And I remove its price from "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
        But I should have original price equal to "£50.00" in "Web-GB" channel
