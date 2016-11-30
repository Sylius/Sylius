@managing_products
Feature: Adding associations to an existing product
    In order to associate my product with others
    As an Administrator
    I want to associate new products to an existing product

    Background:
        Given the store has a product association type "Accessories"
        And the store has "LG G3", "LG headphones" and "LG earphones" products
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding an association to an existing product
        When I want to modify the "LG G3" product
        And I associate as "Accessories" the "LG headphones" and "LG earphones" products
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an association "Accessories" with products "LG headphones" and "LG earphones"
