@applying_catalog_promotions
Feature: Reapplying catalog promotions on variant once its prices changes
    In order to have proper discounts on variants
    As a Store Owner
    I want to have discounts reapplied on variant once its prices changes

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$100.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is another catalog promotion "Christmas sale" available in "Web-US" channel that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Changing the price of the variant
        When I change the price of the "PHP T-Shirt" product variant to "$50.00" in "Web-US" channel
        Then the visitor should still see "$35.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should still see "$100.00" as the original price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Changing the original price of the variant
        When I change the original price of the "PHP T-Shirt" product variant to "$50.00" in "Web-US" channel
        Then the visitor should see "$17.50" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$50.00" as the original price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Removing the original price of the variant
        When I remove the original price of the "PHP T-Shirt" product variant in "Web-US" channel
        Then the visitor should see "$12.25" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$35.00" as the original price of the "T-Shirt" product in the "Web-US" channel
