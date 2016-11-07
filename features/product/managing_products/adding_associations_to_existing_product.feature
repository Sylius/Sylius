@managing_products
Feature: Adding associations to an existing product
    In order to associate my product with others
    As an Administrator
    I want to associate new products to an existing product

    Background:
        Given the store is available in "English (United States)"
        And the store has a product association type "Accessories"
        And the store has "LG G3", "LG headphones" and "LG earphones" products
        And I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Adding an association to an existing product
        When I want to modify this product
        And I associate "LG headphones" and "LG earphones" products as "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "LG G3" should have an association "Accessories" with products "LG headphones" and "LG earphones"
