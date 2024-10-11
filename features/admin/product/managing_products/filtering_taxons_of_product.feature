@managing_products
Feature: Filtering product taxons
    In order to quickly find taxons
    As an Administrator
    I want to search for a specific taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "T-Shirts"
        And the store has a "Shirt" configurable product
        And I am logged in as an administrator

    @ui @no-api @mink:chromedriver
    Scenario: Filtering product taxons
        When I want to modify the "Shirt" product
        And I filter taxons by "T-Shirts"
        Then I should see the "T-Shirts" taxon
        But I should not see the "Clothes" taxon
