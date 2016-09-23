@managing_products
Feature: Adding a new product with images
    In order to extend my merchandise
    As an Administrator
    I want to add a new product to the shop

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new simple product with a single image
        Given I want to create a new simple product
        When I specify its code as "LAMBORGHINI_GALLARDO"
        And I name it "Lamborghini Gallardo Model" in "English (United States)"
        And I set its price to "$100.00"
        And I attach the "lamborghini.jpg" image with a code "banner"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"

    @ui @javascript
    Scenario: Adding a new simple product with multiple images
        Given I want to create a new simple product
        When I specify its code as "LAMBORGHINI_GALLARDO"
        And I name it "Lamborghini Gallardo Model" in "English (United States)"
        And I set its price to "$100.00"
        And I attach the "lamborghini.jpg" image with a code "banner"
        And I attach the "lamborghini.jpg" image with a code "thumbnail"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"
        And this product should have an image with a code "thumbnail"

    @ui @javascript
    Scenario: Adding a new configurable product with a single image
        Given the store has a product option "Model scale" with a code "model_scale"
        And this product option has the "1:43" option value with code "model_scale_medium"
        And this product option has also the "1:18" option value with code "model_scale_big"
        And I want to create a new configurable product
        When I specify its code as "LAMBORGHINI_GALLARDO"
        And I name it "Lamborghini Gallardo Model" in "English (United States)"
        And I add the "Model scale" option to it
        And I attach the "lamborghini.jpg" image with a code "banner"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Lamborghini Gallardo Model" should have an image with a code "banner"
