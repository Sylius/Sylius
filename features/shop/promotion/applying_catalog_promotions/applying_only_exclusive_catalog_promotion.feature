@applying_catalog_promotions
Feature: Applying only exclusive catalog promotion
    In order to buy product with proper promotions
    As a Shop Owner
    I want to be able to set catalog promotion as exclusive

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And there is a catalog promotion "PHP stuff promotion" with priority 100 that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And there is an exclusive catalog promotion "Exclusive PHP stuff promotion" with priority 500 that reduces price by "30%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying only exclusive catalog promotion
        When I view product "T-Shirt"
        Then I should see the product price "$70.00"
        And I should see the product original price "$100.00"

    @api @ui
    Scenario: Applying always single exclusive catalog promotion with highest priority
        Given there is another exclusive catalog promotion "Really exclusive PHP stuff promotion" with priority 1000 that reduces price by "70%" and applies on "PHP T-Shirt" variant
        When I view product "T-Shirt"
        Then I should see the product price "$30.00"
        And I should see the product original price "$100.00"
