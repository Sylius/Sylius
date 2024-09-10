@managing_products
Feature: Checking taxons of a product
    In order to specify in which taxons a product is available
    As an Administrator
    I want to be able to check all or uncheck all taxons of a product

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "T-Shirts"
        And the store has a "Shirt" configurable product
        And the store has a "T-Shirt" configurable product
        And the product "T-Shirt" belongs to taxon "Clothes"
        And the product "T-Shirt" belongs to taxon "T-Shirts"
        And I am logged in as an administrator

    @todo-ui @no-api @mink:chromedriver
    Scenario: Checking all taxons
        When I want to modify the "Shirt" product
        And I check all taxons
        And I save my changes
        Then the product "Shirt" should have the "Clothes" taxon
        And the product "Shirt" should have the "T-Shirts" taxon

    @todo-ui @no-api @mink:chromedriver
    Scenario: Unchecking all taxons
        When I want to modify the "T-Shirt" product
        And I uncheck all taxons
        And I save my changes
        Then the product "Shirt" should not have the "Clothes" taxon
        And the product "Shirt" should not have the "T-Shirts" taxon

    @todo-ui @no-api @mink:chromedriver
    Scenario: Checking all filtered taxons
        When I want to modify the "Shirt" product
        And I filter taxons by "T-Shirts"
        And I check all taxons
        And I save my changes
        Then the product "Shirt" should have the "T-Shirts" taxon
        But the product "Shirt" should not have the "Clothes" taxon

    @todo-ui @no-api @mink:chromedriver
    Scenario: Unchecking all filtered taxons
        When I want to modify the "T-Shirt" product
        And I filter taxons by "T-Shirts"
        And I uncheck all taxons
        And I save my changes
        Then the product "Shirt" should not have the "T-Shirts" taxon
        But the product "Shirt" should have the "Clothes" taxon
