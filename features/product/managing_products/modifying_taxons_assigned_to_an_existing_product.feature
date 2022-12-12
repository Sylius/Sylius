@managing_products
Feature: Modifying taxons assigned to an existing product
    In order to specify in which taxon a product is available
    As an Administrator
    I want to be able to change taxon of a product

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "T-Shirts"
        And the store has a "Shirt" configurable product
        And the store has a "T-Shirt" configurable product
        And the product "T-Shirt" belongs to taxon "Clothes"
        And I am logged in as an administrator

    @ui @api
    Scenario: Modifying taxons assigned to a product
        When I change that the "T-Shirt" product belongs to the "T-Shirts" taxon
        Then the product "T-Shirt" should have the "T-Shirts" taxon

    @ui @api
    Scenario: Adding taxons to product
        When I add "Clothes" taxon to the "Shirt" product
        Then the product "Shirt" should have the "Clothes" taxon
