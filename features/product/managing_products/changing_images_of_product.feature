@managing_products
Feature: Changing images of an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to change images of an existing product

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Changing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "ford.jpg" with "thumbnail" type
        When I want to modify this product
        And I change the image with the "thumbnail" type to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "thumbnail" type

    @ui @javascript
    Scenario: Changing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "ford.jpg" with "thumbnail" type
        When I want to modify this product
        And I change the image with the "thumbnail" type to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "thumbnail" type

    @ui @javascript
    Scenario: Changing the type of image of a simple product
        Given the store has a product "Lamborghini Ford Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And this product has an image "ford.jpg" with "banner" type
        When I want to modify this product
        And I change the first image type to "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should still have 2 images
        But it should not have any images with "thumbnail" type

    @ui @javascript
    Scenario: Changing the type of image of a configurable product
        Given the store has a "Lamborghini Ford Model" configurable product
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And this product has an image "ford.jpg" with "banner" type
        When I want to modify this product
        And I change the first image type to "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should still have 2 images
        But it should not have any images with "thumbnail" type
