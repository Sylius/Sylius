@managing_catalog_promotions
Feature: Seeing catalog promotion's details
    In order to have an overview of one of defined catalog promotions
    As an Administrator
    I want to be able to see its details

    Background:
        Given the store operates on a channel named "Web-US"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-shirt" variant
        And it applies also on "T-Shirt" product
        And the catalog promotion "Winter sale" operates between "2021-11-10" and "2022-01-08"
        And its priority is 1200
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing catalog promotion's details
        When I view details of the catalog promotion "Winter sale"
        Then its name should be "Winter sale"
        And it should reduce price by "30%"
        And it should apply on "PHP T-Shirt" variant
        And it should apply on "T-Shirt" product
        And it should start at "2021-11-10" and end at "2022-01-08"
        And its priority should be 1200

    @ui
    Scenario: Seeing discounted variants on separate page
        When I view details of the catalog promotion "Winter sale"
        And I view discounted variants page
        Then its name should be "Winter sale"
        And I should see a single product variant in the list
        And I should see the product variant "PHP T-Shirt" in the list

    @ui
    Scenario: Being able to access product link
        When I view details of the catalog promotion "Winter sale"
        And I view discounted variants page
        And I follow "T-Shirt" link
        And I should see product show page without variants
