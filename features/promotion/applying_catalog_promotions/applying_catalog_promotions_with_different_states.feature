@applying_catalog_promotions
Feature: Applying catalog promotions with different states
    In order to process proper catalog promotions
    As a Visitor
    I want to see promotions that can be applied

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And the store has a "Mug" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Python Mug" variant priced at "$20.00"
        And there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Python sale" that reduces price by "50%" and applies on "Python Mug" variant
        And catalog promotion "Winter sale" has failed processing

    @api @ui
    Scenario: Seeing catalog promotions that were processed successfully
        When I view product "Mug"
        Then I should see the product price "$10.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Not applying catalog promotion if it failed processing
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
        And I should see this product has no catalog promotion applied
