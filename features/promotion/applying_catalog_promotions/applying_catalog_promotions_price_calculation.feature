@applying_catalog_promotions
Feature: Applying percentage catalog promotions
    In order to check prices calculation
    As a Visitor
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Soft Drinks"
        And the store has a "Orange Juice" product priced at "$20"
        And this product belongs to "Soft Drinks"
        And the store has a "Apple Juice" product priced at "$20.25"
        And this product belongs to "Soft Drinks"
        And the store has a "Peach Juice" product priced at "$10.50"
        And this product belongs to "Soft Drinks"
        And the store has a "Mango Juice" product priced at "$9.94"
        And this product belongs to "Soft Drinks"
        And there is a catalog promotion "Drinks sale" that reduces price by "15%" and applies on "Soft Drinks" taxon

    @api @ui
    Scenario: Discounted price is round
        When I view product "Orange Juice"
        Then I should see the product price "$17.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Discounted price is rounded to a lower last digit
        When I view product "Apple Juice"
        Then I should see the product price "$17.21"
        And I should see the product original price "$20.25"

    @api @ui
    Scenario: Discounted price is rounded to an upper last digit on 4/5 scenario
        When I view product "Peach Juice"
        Then I should see the product price "$8.93"
        And I should see the product original price "$10.50"

    @api @ui
    Scenario: Discounted price is rounded to an upper last digit
        When I view product "Mango Juice"
        Then I should see the product price "$8.45"
        And I should see the product original price "$9.94"
