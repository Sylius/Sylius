@applying_catalog_promotions
Feature: Not reapplying catalog promotions on variants once the product’s taxon changes
    In order to have proper discounts in product catalog
    As a Store Owner
    I do not want to have discounts reapplied on variants once the product’s taxon changes if the catalog promotion criteria are not met

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store classifies its products as "Clothes", "Shirts" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And it is "2022-01-01" now
        And there is a catalog promotion "Winter sale" between "2021-12-20" and "2021-12-30" available in "Web-US" channel that reduces price by "30%" and applies on "Clothes" taxon
        And there is another catalog promotion "Spring sale" between "2022-04-01" and "2022-05-01" available in "Web-US" channel that reduces price by "25%" and applies on "Shirts" taxon
        And there is disabled catalog promotion "Surprise sale" between "2021-07-01" and "2022-05-04" available in "Web-US" channel that reduces price by "90%" and applies on "Dishes" taxon
        And I am logged in as an administrator

    @api @ui
    Scenario: Changing products taxon to taxon with scheduled catalog promotion
        When I change that the "T-Shirt" product does not belong to the "Clothes" taxon
        And I add "Shirts" taxon to the "T-Shirt" product
        Then the visitor should see that the "PHP T-Shirt" variant is not discounted
        And the visitor should still see "$100.00" as the price of the "T-Shirt" product in the "Web-US" channel

    @api @ui
    Scenario: Changing products taxon to taxon with disabled catalog promotion
        When I change that the "T-Shirt" product does not belong to the "Clothes" taxon
        And I add "Dishes" taxon to the "T-Shirt" product
        Then the visitor should see that the "PHP T-Shirt" variant is not discounted
        And the visitor should still see "$100.00" as the price of the "T-Shirt" product in the "Web-US" channel
