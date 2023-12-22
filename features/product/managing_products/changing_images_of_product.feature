@managing_products
Feature: Changing images of an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to change images of an existing product

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @mink:chromedriver @no-api
    Scenario: Changing a single image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "ford.jpg" with "thumbnail" type
        When I want to modify this product
        And I change the image with the "thumbnail" type to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "thumbnail" type

    @ui @mink:chromedriver @no-api
    Scenario: Changing a single image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "ford.jpg" with "thumbnail" type
        When I want to modify this product
        And I change the image with the "thumbnail" type to "lamborghini.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with "thumbnail" type

    @ui @javascript @api
    Scenario: Changing the type of image of a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And this product has an image "ford.jpg" with "banner" type
        When I want to modify this product
        And I change the first image type to "banner"
        And I save my changes to the images
        Then I should be notified that the changes have been successfully applied
        And this product should still have 2 images
        But it should not have any images with "thumbnail" type

    @ui @javascript @api
    Scenario: Changing the type of image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And this product has an image "ford.jpg" with "banner" type
        When I want to modify this product
        And I change the first image type to "banner"
        And I save my changes to the images
        Then I should be notified that the changes have been successfully applied
        And this product should still have 2 images
        But it should not have any images with "thumbnail" type

    @ui @mink:chromedriver @api
    Scenario: Changing the variants of image of a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has "Blue" and "Yellow" variants
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        When I want to modify this product
        And I select "Blue" variant for the first image
        And I save my changes to the images
        Then I should be notified that the changes have been successfully applied
        And this product should still have only one image
        And its image should have "Blue" variant selected

    @no-ui @api
    Scenario: Changing the variants of image of a configurable product
        Given the store has a "Porsche 911 Model" configurable product
        And this product has "Red" and "Green" variants
        And the store has a "Lamborghini Gallardo Model" configurable product
        And this product has "Blue" and "Yellow" variants
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        When I want to modify this product
        And I select "Green" variant for the first image
        And I save my changes to the images
        Then I should be notified that the "Green" variant does not belong to this product
