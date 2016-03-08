@product
Feature: Deleting a product
    In order to remove test, obsolete or incorrect products
    As an Administrator
    I want to be able to delete products from the product catalog

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Toyota GT86 model"
        And this product has "1:43" variant priced at "€15.00"
        And this product has one review

    @todo
    Scenario: Deleted product disappears from the product catalog
        When I delete the "Toyota GT86 model" product
        Then this product should not exist in the product catalog

    @todo
    Scenario: Deleted product variants disappear from the product catalog
        When I delete the "Toyota GT86 model" product
        Then there should be no "1:43" variant of this product in the product catalog

    @todo
    Scenario: Deleted product reviews disappear from the product catalog
        When I delete the "Toyota GT86 model" product
        Then there should be no reviews of this product
