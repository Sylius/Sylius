@viewing_products
Feature: Viewing a product's image on a product details page
    In order to see images of a product
    As a Visitor
    I want to be able to view an image of a single product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "main" type

    @ui @javascript
    Scenario: Viewing a product's image on a product details page
        When I check this product's details
        Then I should see the product name "Lamborghini Gallardo Model"
        And I should see a main image
