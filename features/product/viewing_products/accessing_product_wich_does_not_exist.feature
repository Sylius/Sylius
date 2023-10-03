@viewing_products
Feature: Accessing a product which does not exist
    In order to have a good navigation
    As a Visitor
    I want to be able to be informed that a product does not exits

    Background:
        Given the store operates on a single channel in "United States"

    @ui @api
    Scenario: Accessing a product which does not exist
        When I try to reach unexistent product
        Then I should be informed that the product does not exist
