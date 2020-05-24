@managing_product_variants
Feature: Browsing product variants
    In order to see all product variants
    As an Administrator
    I want to browse product variants of specific product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing product variants in store
        When I want to view all variants of this product
        Then I should see 1 variant in the list

    @ui
    Scenario: Being informed that product variant is not tracked
        When I want to view all variants of this product
        Then I should see that the "Wyborowa Vodka Exquisite" variant is not tracked

    @ui
    Scenario: Being informed about on hand quantity of a product variant
        Given the "Wyborowa Vodka Exquisite" product variant is tracked by the inventory
        When I want to view all variants of this product
        Then I should see that the "Wyborowa Vodka Exquisite" variant has zero on hand quantity

    @ui
    Scenario: Being informed that product variant is enabled
        When I want to view all variants of this product
        Then I should see that the "Wyborowa Vodka Exquisite" variant is enabled
