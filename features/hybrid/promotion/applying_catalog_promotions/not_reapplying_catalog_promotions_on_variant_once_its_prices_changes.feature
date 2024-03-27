@applying_catalog_promotions
Feature: Not reapplying catalog promotions on variant once its prices changes
    In order to have proper discounts on variants
    As a Store Owner
    I do not want to have discounts reapplied on variant once its prices changes if the catalog promotion criteria are not met

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And it is "2022-01-01" now
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$100.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" between "2021-12-20" and "2021-12-30" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is another catalog promotion "Spring sale" between "2022-04-01" and "2022-05-01" available in "Web-US" channel that reduces price by "25%" and applies on "PHP T-Shirt" variant
        And there is disabled catalog promotion "Surprise sale" between "2021-07-01" and "2022-05-04" available in "Web-US" channel that reduces price by "90%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui
    Scenario: Changing the price of the variant
        When I change the price of the "PHP T-Shirt" product variant to "$50.00" in "Web-US" channel
        Then the visitor should see "$50.00" as the price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Changing the original price of the variant
        When I change the original price of the "PHP T-Shirt" product variant to "$105.00" in "Web-US" channel
        Then the visitor should see "$100.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$105.00" as the original price of the "T-Shirt" product in the "Web-US" channel
