@managing_products
Feature: Deleting a product
    In order to remove test, obsolete or incorrect products
    As an Administrator
    I want to be able to delete products from the product catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lamborghini Gallardo model"
        And this product has "1:43" variant priced at "$15.00"
        And this product has one review from customer "john@doe.com"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting product from the product catalog
        When I delete the "Lamborghini Gallardo model" product
        Then I should be notified that it has been successfully deleted
        And this product should not exist in the product catalog

    @ui
    Scenario: Deleting used product should not be possible
        Given there is a customer "batman@dc.com" that placed an order
        And the customer bought a single "Lamborghini Gallardo model"
        When I delete the "Lamborghini Gallardo model" product
        Then I should be notified that this product cannot be deleted
        And the product "Lamborghini Gallardo model" should still be in the shop

    @ui @javascript
    Scenario: Deleting used product should not remove the image
        Given this product has an image "lamborghini.jpg" with "thumbnail" type
        And there is a customer "batman@dc.com" that placed an order
        And the customer bought a single "Lamborghini Gallardo model"
        When I delete the "Lamborghini Gallardo model" product
        Then I should be notified that this product cannot be deleted
        And this product should still have an image with "thumbnail" type

    @domain
    Scenario: Deleted product variants disappear from the product catalog
        When I delete the "Lamborghini Gallardo model" product
        Then there should be no variants of this product in the product catalog
