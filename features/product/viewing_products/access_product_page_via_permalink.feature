@viewing_products
Feature: Viewing a product details using permalink
    In order to see products detailed page
    As a visitor
    I want to be able to have access to product page by permalink

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Access to detailed product page using permalink
        Given the store has a product "T-shirt banana"
        When I open page "en_US/products/t-shirt-banana"
        Then I should be on "T-shirt banana" product detailed page
        And I should see the product name "T-shirt banana"
