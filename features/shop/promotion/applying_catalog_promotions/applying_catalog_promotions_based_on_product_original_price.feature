@applying_catalog_promotions
Feature: Applying catalog promotions based on the product's original price
    In order to see proper discounts independent of previous promotions
    As a Visitor
    I want to see discounted products in the catalog based on their original price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt"
        And this product's price is "$100.00"
        And the product "T-Shirt" has original price "$120.00"
        And there is a catalog promotion "Winter sale" that reduces price by "90%" and applies on "T-Shirt" variant

    @api @ui
    Scenario: Applying simple catalog promotions
        When I view product "T-Shirt"
        Then I should see the product price "$12.00"
        And I should see the product original price "$120.00"
