@managing_product_variants
Feature: Removing a product variant's price from disabled channel
    In order to have only valid prices on enabled channels
    As an Administrator
    I want to be able to remove price from disabled channels

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "USD" currency
        And the store has a "PHP Mug" configurable product
        And this product has "Medium PHP Mug" variant priced at "$20" in "Web-US" channel
        And "Medium PHP Mug" variant priced at "$25" in "Web-GB" channel
        And the channel "Web-GB" has been disabled
        And this product is disabled in "Web-GB" channel
        And I am logged in as an administrator

    @ui
    Scenario: Removing a product variant 's price from disabled channel
        When I want to modify the "Medium PHP Mug" product variant
        And I remove its price for "Web-GB" channel
        And I save my changes
        Then I should not have configured price for "Web-GB" channel
