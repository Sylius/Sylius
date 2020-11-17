@managing_products
Feature: Adding svg file to an existing product
    In order to add svg file to my product
    As an Administrator
    I want to be able to add new svg file to product

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a single image to an existing product
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "batman.svg" image with "svg" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an svg file with "svg" type
