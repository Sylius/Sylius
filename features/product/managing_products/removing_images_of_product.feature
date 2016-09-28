@managing_products
Feature: Removing images of an existing product
    In order to get rid of obsolete images of my products
    As an Administrator
    I want to be able to remove images from an existing product

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Removing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have images

    @ui @javascript
    Scenario: Removing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have images

    @ui @javascript
    Scenario: Removing all images of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        And this product has also an image "lamborghini.jpg" with a code "main"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I remove also an image with a code "main"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have images

    @ui @javascript
    Scenario: Removing all images of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        And this product has also an image "lamborghini.jpg" with a code "main"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I remove also an image with a code "main"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have images

    @ui @javascript
    Scenario: Removing only one image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        And this product has also an image "lamborghini.jpg" with a code "main"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "main"
        But this product should not have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Removing only one image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with a code "thumbnail"
        And this product has also an image "lamborghini.jpg" with a code "main"
        When I want to modify this product
        And I remove an image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "main"
        But this product should not have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with a code "thumbnail"
        And I attach the "lamborghini.jpg" image with a code "main"
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "main"
        But this product should not have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with a code "thumbnail"
        And I attach the "lamborghini.jpg" image with a code "main"
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "main"
        But this product should not have an image with a code "thumbnail"
