@viewing_products
Feature: Accessing a product which does not exist
    In order to have a good navigation
    As a visitor
    I want to be able to be informed that a product does not exits

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Accessing a product which does not exist
        Given the store has a product "T-shirt banana"
        When I open page "en_US/products/44 Magnum"
        Then I should be informed that the product does not exist
