@managing_product_variants
Feature: Seeing correct option values while editing product variant
    In order to edit product variant
    As an Administrator
    I want to see option values when editing product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And this product has option "Taste" with values "Orange", "Melon" and "Cactus"
        And this product has option "Type" with values "Clear" and "Color"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing default option values while editing product variant in store
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I should see the "Type" option as "Clear"
        And I should see the "Taste" option as "Orange"

    @ui @no-api
    Scenario: Seeing changed option values while editing product variant in store
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I change its "Taste" option to "Melon"
        And I change its "Type" option to "Color"
        And I save my changes
        And I want to modify the "Wyborowa Vodka Exquisite" product variant
        Then I should see the "Taste" option as "Melon"
        And I should see the "Type" option as "Color"
