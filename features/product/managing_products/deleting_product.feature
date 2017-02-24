@managing_products
Feature: Deleting a product
    In order to remove test, obsolete or incorrect products
    As an Administrator
    I want to be able to delete products from the product catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Toyota GT86 model"
        And this product has "1:43" variant priced at "$15.00"
        And this product has one review from customer "john@doe.com"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted product disappears from the product catalog
        When I delete the "Toyota GT86 model" product
        Then I should be notified that it has been successfully deleted
        And this product should not exist in the product catalog

    @domain
    Scenario: Deleted product variants disappear from the product catalog
        When I delete the "Toyota GT86 model" product
        Then there should be no variants of this product in the product catalog
