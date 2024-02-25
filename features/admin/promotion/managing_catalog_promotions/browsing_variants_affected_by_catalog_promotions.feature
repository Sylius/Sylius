@managing_catalog_promotions
Feature: Browsing variants affected by catalog promotions
    In order to have an overview of all product variants affected by a catalog promotion
    As an Administrator
    I want to be able to browse list of them

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes"
        And the store has a product "Pants" priced at "$30.00"
        And this product belongs to "Clothes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Sylius T-Shirt" variant priced at "$15.00"
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing product variants affected by a catalog promotion applied on variants
        Given there is a catalog promotion "PHP T-Shirt promotion" that reduces price by "50%" and applies on "PHP T-Shirt" variant
        When I browse variants affected by catalog promotion "PHP T-Shirt promotion"
        Then there should be 1 product variant on the list
        And it should be the "PHP T-Shirt" product variant

    @ui @api
    Scenario: Browsing product variants affected by a catalog promotion applied on products
        Given there is a catalog promotion "T-Shirt promotion" that reduces price by "20%" and applies on "T-Shirt" product
        When I browse variants affected by catalog promotion "T-Shirt promotion"
        Then there should be 2 product variants on the list
        And it should be "PHP T-Shirt" and "Sylius T-Shirt" product variants

    @ui @api
    Scenario: Browsing product variants affected by a catalog promotion applied on taxons
        Given there is a catalog promotion "Clothes promotion" that reduces price by "10%" and applies on "Clothes" taxon
        When I browse variants affected by catalog promotion "Clothes promotion"
        Then there should be 3 product variants on the list

    @ui @api
    Scenario: Browsing product variants affected by a catalog promotion when its scope overlaps with an exclusive promotion
        Given there is a catalog promotion "T-Shirt promotion" that reduces price by "20%" and applies on "T-Shirt" product
        And there is an exclusive catalog promotion "Sylius T-Shirt promotion" with priority 100 that reduces price by "30%" and applies on "Sylius T-Shirt" variant
        When I browse variants affected by catalog promotion "T-Shirt promotion"
        Then there should be 1 product variant on the list
        And it should be the "PHP T-Shirt" product variant

    @ui @no-api
    Scenario: Accessing the product details page through affected product variants list
        Given there is a catalog promotion "PHP T-Shirt promotion" that reduces price by "50%" and applies on "PHP T-Shirt" variant
        When I browse variants affected by catalog promotion "PHP T-Shirt promotion"
        And I want to view the product of variant "PHP T-Shirt"
        Then I should be viewing the details of product "T-Shirt"
