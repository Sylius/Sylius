@managing_products
Feature: Adding a new product with associations
    In order to associate my product with others
    As an Administrator
    I want to add a new product with associated products

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Accessories" and "Alternatives" product association types
        And the store has "LG headphones", "LG earphones", "LG G4" and "LG G5" products
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new product with associations
        When I want to create a new simple product
        And I specify its code as "lg_g3"
        And I name it "LG G3" in "English (United States)"
        And I set its price to "$400.00" for "United States" channel
        And I associate as "Accessories" the "LG headphones" and "LG earphones" products
        And I associate as "Alternatives" the "LG G4" and "LG G5" products
        And I add it
        Then I should be notified that it has been successfully created
        And this product should have an association "Accessories" with products "LG headphones" and "LG earphones"
        And this product should also have an association "Alternatives" with products "LG G4" and "LG G5"
        And the product "LG G3" should appear in the store

    @ui @javascript
    Scenario: Adding a new product with associations after changing associated items
        When I want to create a new simple product
        And I specify its code as "lg_g3"
        And I name it "LG G3" in "English (United States)"
        And I set its price to "$400.00" for "United States" channel
        And I associate as "Accessories" the "LG headphones" and "LG earphones" products
        And I remove an associated product "LG earphones" from "Accessories"
        And I add it
        Then I should be notified that it has been successfully created
        And this product should have an association "Accessories" with product "LG headphones"
        And this product should not have an association "Accessories" with product "LG earphones"
        And the product "LG G3" should appear in the store
