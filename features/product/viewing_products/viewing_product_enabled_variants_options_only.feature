@viewing_products
Feature: Viewing product's enabled variants only
    In order to buy only available products
    As a Customer
    I want to see only enabled product variants options

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has option "Size" with values "Small", "Medium" and "Large"
        And this product has option "Color" with values "Blue", "Green" and "Yellow"
        And this product has all possible variants
        But all the product variants with the "Yellow" color are disabled

    @ui
    Scenario: Seeing only enabled variants options
        When I view product "Super Cool T-Shirt"
        Then I should not be able to select the "Yellow" color option value
