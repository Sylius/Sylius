@managing_products
Feature: Preventing deletion of purchased product
    In order to maintain proper order history
    As an Administrator
    I want to be prevented from deleting purchased products

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Toyota GT86 model"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000027"
        And the customer bought a single "Toyota GT86 model"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @domain @ui @api
    Scenario: Purchased product cannot be deleted
        When I try to delete the "Toyota GT86 model" product
        Then I should be notified that this product is in use and cannot be deleted
        And this product should still exist in the product catalog

    @ui @api
    Scenario: Purchased product images should be kept
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has an image "lamborghini.jpg" with "thumbnail" type
        And there is a customer "jane.doe@gmail.com" that placed an order "#00000026"
        And the customer bought a single "Lamborghini Gallardo Model"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I try to delete the "Lamborghini Gallardo Model" product
        Then I should be notified that this product is in use and cannot be deleted
        And this product should still exist in the product catalog
        And the product "Lamborghini Gallardo Model" should still have an accessible image
