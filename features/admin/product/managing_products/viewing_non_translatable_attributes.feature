@managing_products
Feature: Viewing product's non translatable attributes on edit page
    In order to see product's non translatable attribute
    As an Administrator
    I want to be able to see product's single non translatable attribute

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Pickaxe"
        And this product has non-translatable percent attribute "crit chance" with value 10%
        And I am logged in as an administrator

    @ui @api
    Scenario: Viewing product's attributes defined in different locales
        When I modify the "Iron Pickaxe" product
        And I should see non-translatable attribute "crit chance" with value 10%
