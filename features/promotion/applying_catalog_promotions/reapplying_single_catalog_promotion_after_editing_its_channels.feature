@applying_catalog_promotions
Feature: Reapplying single catalog promotion after editing its channels
    In order to have proper discounts in different channels
    As a Store Owner
    I want to have discounts reapplied in product catalog once the channels of catalog promotion changes

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store operates on another channel named "Web-GB" with hostname "web-gb"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And "PHP T-Shirt" variant priced at "$30.00" in "Web-GB" channel
        And this product is available in "Web-US" channel and "Web-GB" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Removing applied catalog promotion after removing its channel
        When I make this catalog promotion unavailable in the "Web-US" channel
        Then the visitor should see "$20.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see this variant is not discounted

    @api @ui
    Scenario: Reapplying catalog promotion after adding new channel to them
        When I make this catalog promotion available in the "Web-GB" channel
        Then the visitor should see "$21.00" as the price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should see "$30.00" as the original price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should still see "$14.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should still see "$20.00" as the original price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Reapplying catalog promotion after switching availability in channels
        When I switch this catalog promotion availability from the "Web-US" channel to the "Web-GB" channel
        Then the visitor should see "$20.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see this variant is not discounted
        And the visitor should see "$21.00" as the price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should see "$30.00" as the original price of the "T-Shirt" product in the "Web-GB" channel
