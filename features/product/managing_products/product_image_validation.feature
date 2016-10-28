@managing_products
Feature: Product image validation
    In order to avoid making mistakes when managing a product images
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new image without specifying its code to a simple product
        Given the store has a product "Lamborghini Gallardo Model"
        When I want to modify the "Lamborghini Gallardo Model" product
        And I attach the "lamborghini.jpg" image without a code
        And I try to save my changes
        Then I should be notified that an image code is required
        And this product should not have any images

    @ui @javascript
    Scenario: Trying to add a new image without specifying its code to a configurable product
        Given the store has a "Lamborghini Gallardo Model" configurable product
        When I want to modify the "Lamborghini Gallardo Model" product
        And I attach the "lamborghini.jpg" image without a code
        And I try to save my changes
        Then I should be notified that an image code is required
        And this product should not have any images
