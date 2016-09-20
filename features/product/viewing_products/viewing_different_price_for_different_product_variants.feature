@viewing_products
Feature: Viewing different price for different product variants
    In order to see product variant price
    As a Visitor
    I want to be able to see proper price for each product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$4.00"

    @ui
    Scenario: Viewing a detailed page with default variant's price
        When I check product "Wyborowa Vodka" details
        Then I should see the product price "$40.00"

    @ui @javascript
    Scenario: Viewing a detailed page with product's price for different variant
        When I check product "Wyborowa Vodka" details
        And I select "Wyborowa Apple" variant
        Then I should see the product price "$4.00"
