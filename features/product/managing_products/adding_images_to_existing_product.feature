@managing_products
Feature: Adding images to an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to add new images to a taxon

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a single image to an existing product
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type

    @ui @javascript
    Scenario: Adding multiple images to an existing product
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with "banner" type
        And I attach the "lamborghini.jpg" image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type
        And it should also have an image with "thumbnail" type

    @ui @javascript
    Scenario: Adding multiple images of the same type to an existing product
        Given the store has a product "Lamborghini Ford Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with "banner" type
        And I attach the "ford.jpg" image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have 2 images

    @ui @javascript
    Scenario: Adding a single image to an existing configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type

    @ui @javascript
    Scenario: Adding multiple images of the same type to an existing configurable product
        Given the store has a "Lamborghini Ford Model" configurable product
        And I want to modify this product
        When I attach the "lamborghini.jpg" image with "banner" type
        And I attach the "ford.jpg" image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have 2 images

    @ui @javascript
    Scenario: Adding an image to an existing product without providing its type
        Given the store has a product "Lamborghini Gallardo Model"
        And I want to modify this product
        When I attach the "lamborghini.jpg" image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have only one image

    @ui @javascript
    Scenario: Adding an image to an existing configurable product without providing its type
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And I want to modify this product
        When I attach the "lamborghini.jpg" image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have only one image
