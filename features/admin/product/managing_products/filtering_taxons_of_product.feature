@managing_products
Feature: Filtering product taxons
    In order to quickly find taxons
    As an Administrator
    I want to search for a specific taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "T-Shirts"
        And the store has a "Shirt" configurable product
        And the store has a "T-Shirt" configurable product
        And the product "T-Shirt" belongs to taxon "Clothes"
        And the product "T-Shirt" belongs to taxon "T-Shirts"
        And I am logged in as an administrator

    @ui @no-api @mink:chromedriver
    Scenario: Filter product taxons
        When I want to modify the "Shirt" product
        And I filter taxons by "T-Shirts"
        Then I should see the "T-Shirts" taxon
        And I should not see the "Clothes" taxon
