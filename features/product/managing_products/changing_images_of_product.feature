@managing_products
Feature: Changing images of an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to changing images of an existing product

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Changing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "ford.jpg" with a code "thumbnail"
        When I want to modify this product
        And I change the image with the "thumbnail" code to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Changing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "ford.jpg" with a code "thumbnail"
        When I want to modify this product
        And I change the image with the "thumbnail" code to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "thumbnail"

    @ui
    Scenario: Unable to change an image's code of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        When I want to modify this product
        Then the image code field should be disabled

    @ui
    Scenario: Unable to change an image's code of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        When I want to modify this product
        Then the image code field should be disabled
