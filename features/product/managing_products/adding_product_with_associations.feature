@managing_products
Feature: Adding a new product with associations
    In order to associate my product with others
    As an Administrator
    I want to add a new product with associated products

    Background:
        Given the store is available in "English (United States)"
        And the store has "Accessories" and "Alternatives" product association types
        And the store has "LG headphones", "LG earphones", "LG G4" and "LG G5" products
        And I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Adding a new product with associations
        When I want to create a new simple product
        And I specify its code as "lg_g3"
        And I name it "LG G3" in "English (United States)"
        And I set its price to "$400.00"
        And I associate "LG headphones" and "LG earphones" products as "Accessories"
        And I associate "LG G4" and "LG G5" products as "Alternatives"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "LG G3" should have an association "Accessories" with products "LG headphones" and "LG earphones"
        And the product "LG G3" should also have an association "Alternatives" with products "LG G4" and "LG G5"
        And the product "LG G3" should appear in the shop
