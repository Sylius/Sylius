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
        When I change that the "T-Shirt" product does not belong to the "Clothes" taxon
        And I add "T-Shirts" taxon to the "T-Shirt" product
        Then the product "T-Shirt" should have the "T-Shirts" taxon

    @ui @api
    Scenario: Adding taxons to product
        When I add "Clothes" taxon to the "Shirt" product
        Then the product "Shirt" should have the "Clothes" taxon

    @api @no-ui
    Scenario: Being prevented from adding the same taxon twice
        When I try to add "Clothes" taxon to the "T-Shirt" product
        Then I should be notified that product taxons cannot be duplicated

    @api @no-ui
    Scenario: Being prevented from assigning an empty taxon to a product
        When I try to assign an empty taxon to the "T-Shirt" product
        Then I should be notified that specifying a taxon is required

    @api @no-ui
    Scenario: Being prevented from assigning an empty product to a taxon
        When I try to assign an empty product to the "Clothes" taxon
        Then I should be notified that specifying a product is required

    @api @no-ui
    Scenario: Being unable duplicate a product taxon by getting it from another product
        Given the product "Shirt" belongs to taxon "Clothes"
        When I try to assign the product taxon of product "T-Shirt" and taxon "Clothes" to the product "Shirt"
        Then I should be notified that product taxons cannot be duplicated
