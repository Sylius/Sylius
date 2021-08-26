@managing_catalog_promotions
Feature: Adding catalog promotion with a rule
    In order to set up a catalog promotion for chosen part of catalog
    As an Administrator
    I want to be able to set a rule for a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api
    Scenario: Creating catalog promotion with percentage discount
        When I want to create new catalog promotion
        And I specify its code as "10%_for_products"
        And I name it "10% Discount"
        And I add the "percentage_product_discount" catalog promotion action configured with amount of "10%"
        And I add it
        Then I should be notified that it has been successfully created
        And the "10% Discount" catalog promotion should appear in the registry with "percentage_product_discount" action
