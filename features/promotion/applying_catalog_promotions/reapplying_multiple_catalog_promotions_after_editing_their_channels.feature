@applying_catalog_promotions
Feature: Reapplying multiple catalog promotions after editing their channels
    In order to have proper discounts in different channels
    As a Store Owner
    I want to have discounts reapplied in product catalog once the channels of catalog promotion changes

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store operates on another channel named "Web-GB" with hostname "web-gb"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$100.00" in "Web-US" channel
        And "PHP T-Shirt" variant priced at "$100.00" in "Web-GB" channel
        And this product is available in "Web-US" channel and "Web-GB" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is another catalog promotion "Christmas sale" available in "Web-US" channel and "Web-GB" channel that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Removing only modified catalog promotion after removing its channel
        When I make the "Winter sale" catalog promotion unavailable in the "Web-US" channel
        Then the visitor should see "$50.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Reapplying catalog promotion after adding new channel to them
        When I make "Winter sale" catalog promotion available in the "Web-GB" channel
        Then the visitor should see "$35.00" as the price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should still see "$35.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should still see "$100.00" as the original price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Reapplying catalog promotion after switching availability in channels
        When I switch "Winter sale" catalog promotion availability from the "Web-US" channel to the "Web-GB" channel
        Then the visitor should see "$50.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$35.00" as the price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-GB" channel

    @api @ui
    Scenario: Reapplying catalog promotion after switching availability in channels
        When I make the "Christmas sale" catalog promotion unavailable in the "Web-US" channel
        Then the visitor should see "$70.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$50.00" as the price of the "T-Shirt" product in the "Web-GB" channel
        And the visitor should see "$100.00" as the original price of the "T-Shirt" product in the "Web-GB" channel
