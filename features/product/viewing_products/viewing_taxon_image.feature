@viewing_products
Feature: Viewing a product's image on a product details page
    In order to see images of a product
    As a Visitor
    I want to be able to view an image of a single product

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category"
        And the "Category" taxon has an image "lamborghini.jpg" with "main" type

    @api @no-ui
    Scenario: Viewing a taxon's image
        When I check the "Category" taxon's details
        Then I should see the taxon name "Category"
        And I should see a main image
