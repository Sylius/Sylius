@viewing_products
Feature: Viewing a product's images on a product details page
    In order to see images of a product
    As a Visitor
    I want to be able to view a product's images

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "other" type at position 2
        And this product has an image "lamborghini.jpg" with "main" type at position 1

    @api @ui @javascript
    Scenario: Viewing a product's main image on a product details page
        When I view product "Lamborghini Gallardo Model"
        Then I should see the product name "Lamborghini Gallardo Model"
        And I should be able to see a main image of type "main"

    @api @ui @javascript
    Scenario: Viewing a products images in correct order
        When I view product "Lamborghini Gallardo Model"
        Then I should see the product name "Lamborghini Gallardo Model"
        And the main image should be of type "main"
        And the first thumbnail image should be of type "main"
        And the second thumbnail image should be of type "other"
