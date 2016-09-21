@managing_products
Feature: Adding images to an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to add new images to a taxon

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a single image to an existing simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"

    @ui @javascript
    Scenario: Adding multiple images to an existing taxon
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with a code "banner"
        And I attach the "lamborghini.jpg" image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"
        And this product should have also an image with a code "thumbnail"

    @ui @javascript
    Scenario: Adding a single image to an existing configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"
