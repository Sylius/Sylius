@viewing_products
Feature: Viewing different price for different product variants selected with options
    In order to see product variant price
    As a Visitor
    I want to be able to see proper price for each product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Volume" with values "0,5" and "0,7"
        And this product is available in "0,5" volume priced at "$20.00"
        And this product is available in "0,7" volume priced at "$25.00"

    @ui
    Scenario: Viewing a detailed page with product's price
        When I check product "Wyborowa Vodka" details
        Then I should see the product price "$20.00"

    @ui @javascript
    Scenario: Viewing a detailed page with product's price for different option
        When I check product "Wyborowa Vodka" details
         And I set its volume to "0,7"
        Then I should see the product price "$25.00"
