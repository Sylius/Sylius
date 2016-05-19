@managing_product_variants
Feature: Browsing product variants
    In order to see all product variants
    As an Administrator
    I want to browse product variants of specific product

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "€40.00"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing product variants in store
        When I want to view all variants of this product
        Then I should see 1 variant in the list
