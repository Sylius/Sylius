@managing_products
Feature: Adding images to an existing product
    In order to change images of my product
    As an Administrator
    I want to be able to add new images to a taxon

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a single image to an existing product
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "banner" type to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type

    @ui @mink:chromedriver @api
    Scenario: Adding multiple images to an existing product
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "banner" type to this product
        And I attach the "lamborghini.jpg" image with "thumbnail" type to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type
        And it should also have an image with "thumbnail" type

    @ui @javascript @api
    Scenario: Adding multiple images of the same type to an existing product
        Given the store has a product "Lamborghini Ford Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "banner" type to this product
        And I attach the "ford.jpg" image with "banner" type to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this product should have 2 images

    @ui @mink:chromedriver @api
    Scenario: Adding a single image to an existing configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "banner" type to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And the product "Lamborghini Gallardo Model" should have an image with "banner" type

    @ui @javascript @api
    Scenario: Adding multiple images of the same type to an existing configurable product
        Given the store has a "Lamborghini Ford Model" configurable product
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with "banner" type to this product
        And I attach the "ford.jpg" image with "banner" type to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this product should have 2 images

    @ui @javascript @api
    Scenario: Adding an image to an existing product without providing its type
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify this product
        And I attach the "lamborghini.jpg" image to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this product should have only one image

    @ui @javascript @api
    Scenario: Adding an image to an existing configurable product without providing its type
        Given the store has a "Lamborghini Gallardo Model" configurable product
        When I want to modify this product
        And I attach the "lamborghini.jpg" image to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this product should have only one image

    @ui @mink:chromedriver @api
    Scenario: Adding an image to an existing configurable product with selecting a variant
        Given the store has a "Lamborghini Gallardo Model" configurable product
        And this product has "Blue" and "Yellow" variants
        When I want to modify this product
        And I attach the "lamborghini.jpg" image with selected "Yellow" variant to this product
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this product should have only one image
        And its image should have "Yellow" variant selected
