@comparing_products
Feature: Adding product to the comparison
    In order to add product to the comparison
    As a Viewer
    I want to see compared products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius Mug"
        And the store has a product "Sylius Cup"
        And the store has a product "Sylius T-Shirt"

    @ui
    Scenario: Adding product to the comparison
        Given there is a product "Sylius Mug" with "Color" attribute set to "Red"
        And there is a product "Sylius Cup" with "Color" attribute set to "Blue"
        When I compare these products
        Then I should see list of compared product attributes
