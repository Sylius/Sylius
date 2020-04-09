@viewing_products
Feature: Viewing different price for different product variants selected with options
    In order to see product variant price
    As a Visitor
    I want to be able to see a proper price for each product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Volume" with values "0,5L", "0,7L" and "1L"
        And this product is available in "0,5L" volume priced at "$20.00"
        And this product is available in "0,7L" volume priced at "$25.00"

    @ui
    Scenario: Viewing a detailed page with product's price
        When I view product "Wyborowa Vodka"
        Then I should see the product price "$20.00"

    @ui @javascript
    Scenario: Viewing a detailed page with product's price for different option
        When I view product "Wyborowa Vodka"
        And I set its volume to "0,7L"
        Then I should see the product price "$25.00"
