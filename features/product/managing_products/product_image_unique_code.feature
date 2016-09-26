@managing_products
Feature: Product image unique code validation within a product
    In order to uniquely identify images within a product
    As an Administrator
    I want to be prevented from adding two images with the same code to the same product

    Background:
        Given the store is available in "English (United States)"
        And the store has "Lamborghini Gallardo Model" and "Ford Capri Model" products
        And the "Lamborghini Gallardo Model" product has an image "lamborghini.jpg" with a code "thumbnail"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding images with the same code to different products
        When I want to modify the "Ford Capri Model" product
        And I attach the "ford.jpg" image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Trying to add an image with a code that is already used by other image of this product
        When I want to modify the "Lamborghini Gallardo Model" product
        And I attach the "ford.jpg" image with a code "thumbnail"
        And I try to save my changes
        Then I should be notified that the image with this code already exists
        And there should still be only one image in the "Lamborghini Gallardo Model" product
