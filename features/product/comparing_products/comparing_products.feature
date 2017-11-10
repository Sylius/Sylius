@comparing_products
Feature: Adding product to the comparison
    In order to make more reasonable choice of product
    As a Visitor
    I want to see comparison of products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius Mug"
        And the store has a product "Sylius Cup"
        And the store has a product "Sylius T-Shirt"

    @ui
    Scenario: comparing two products with common attributes
        Given There is a product "Sylius Mug" with "Color" attribute set to "Red"
        And There is a product "Sylius Cup" with "Color" attribute set to "Blue"
        When I compare these products
        Then I should see list of compared product attributes

    @ui
    Scenario: comparing two products with uncommon attributes
        Given There is a product "Sylius T-Shirt" with "Size" attribute set to "XL"
        And There is a product "Sylius Mug" with "Color" attribute set to "Black"
        When I compare these products
        Then I should see empty comparison list

    @ui
    Scenario: comparing too much products
        Given there is a product "Sylius T-Shirt" with "Size" attribute set to "XL"
        And there is a product "Sylius T-Shirt" with "Size" attribute set to "L"
        And there is a product "Sylius T-Shirt" with "Size" attribute set to "M"
        And there is a product "Sylius T-Shirt" with "Size" attribute set to "S"
        And there is a product "Sylius T-Shirt" with "Size" attribute set to "XS"
        When I compare these products
        Then I should see an error about the products count in comparison limit