@viewing_products
Feature: Viewing a product details using permalink
    In order to see products detailed page
    As a visitor
    I want to be able to have access to product page by permalink

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana"

    @ui @no-api
    Scenario: Accessing a detailed product page using permalink
        When I open page "en_US/products/t-shirt-banana"
        Then I should be on "T-shirt banana" product detailed page
        And I should see the product name "T-shirt banana"

    @api
    Scenario: Viewing a detailed page with product's slug
        When I view product "T-shirt banana" using slug
        Then I should be redirected to "T-shirt banana" product
