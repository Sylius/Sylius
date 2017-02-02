@managing_products
Feature: Removing images of an existing product
    In order to get rid of obsolete images of my products
    As an Administrator
    I want to be able to remove images from an existing product

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Removing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have any images

    @ui @javascript
    Scenario: Removing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have any images

    @ui @javascript
    Scenario: Removing all images of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And it also has an image "lamborghini.jpg" with "main" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I also remove an image with "main" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have any images

    @ui @javascript
    Scenario: Removing all images of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And it also has an image "lamborghini.jpg" with "main" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I also remove an image with "main" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should not have any images

    @ui @javascript
    Scenario: Removing only one image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And it also has an image "lamborghini.jpg" with "main" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "main" type
        But it should not have any images with "thumbnail" type

    @ui @javascript
    Scenario: Removing only one image of a simple product when all images have same type
        Given the store has a product "Lamborghini Ford Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And it also has an image "ford.jpg" with "thumbnail" type
        When I want to modify this product
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have only one image

    @ui @javascript
    Scenario: Removing only one image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And it also has an image "lamborghini.jpg" with "main" type
        When I want to modify this product
        And I remove an image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "main" type
        But it should not have any images with "thumbnail" type

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "thumbnail" type
        And I attach the "lamborghini.jpg" image with "main" type
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have only one image
        And this product should have an image with "main" type
        But it should not have any images with "thumbnail" type

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "thumbnail" type
        And I attach the "lamborghini.jpg" image with "main" type
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "main" type
        But it should not have any images with "thumbnail" type
