@managing_catalog_promotions
Feature: Browsing catalog promotions
    In order to have an overview of one of defined catalog promotions
    As an Administrator
    I want to be able to see its details

    Background:
        Given the store operates on a channel named "Web-US"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-shirt" variant
        And the catalog promotion "Winter sale" operates between "2021-11-10" and "2022-01-08"
        And I am logged in as an administrator

    @api
    Scenario: Seeing catalog promotion's details
        When I view details of the catalog promotion "Winter sale"
        Then its name should be "Winter sale"
        And it should reduce price by "30%"
        And it should apply on "PHP T-shirt" variant
        And it should operate between "2021-11-10" and "2022-01-08"
