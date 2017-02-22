@managing_products
Feature: Deleting a product with its reviews
    In order to remove test, obsolete or incorrect products
    As an Administrator
    I want to be able to delete products from the product catalog

    Background:
        Given the store has a product "Toyota GT86 model"
        And this product has one review from customer "john@doe.com"
        And I am logged in as an administrator

    @domain @ui
    Scenario: Deleted product reviews disappear from the product catalog
        When I delete the "Toyota GT86 model" product
        Then I should be notified that it has been successfully deleted
        And there should be no reviews of this product
