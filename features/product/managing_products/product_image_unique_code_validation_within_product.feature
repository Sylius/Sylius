@managing_products
Feature: Product's images unique code validation within a product
    In order to uniquely identify images within a product
    As an Administrator
    I want to be prevented from adding two images with the same code to the same product

    Background:
        Given I am logged in as an administrator
        And the store has a product "Toy lamborghini"
        And this product has an image "lamborghini.jpg" with a code "lamborghini"
        And the store has a product "Car T-shirt"

    @ui @javascript
    Scenario: Adding images with the same code to different product
        When I want to modify the "Car T-shirt" product
        And I attach the "t-shirts.jpg" image with a code "lamborghini"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an image with a code "lamborghini"

    @ui @javascript
    Scenario: Trying to add an image with a code that is already used by other image of this product
        When I want to modify the "Toy lamborghini" product
        And I attach the "t-shirts.jpg" image with a code "lamborghini"
        And I try to save my changes
        Then I should be notified that the image with this code already exists
        And there should still be only one image in the "Toy lamborghini" product

    @ui @javascript
    Scenario: Trying to add images with the same code
        When I want to modify the "Car T-shirt" product
        And I attach the "t-shirts.jpg" image with a code "lamborghini"
        And I attach the "lamborghini.jpg" image with a code "lamborghini"
        And I try to save my changes
        Then I should be notified that the 1st image should have an unique code
        And I should be notified that the 2nd image should have an unique code
