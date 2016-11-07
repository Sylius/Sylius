@managing_products
Feature: Changing associations of an existing product
    In order to change associations of my product
    As an Administrator
    I want to be able to change associations of an existing product

    Background:
        Given the store is available in "English (United States)"
        And the store has a product association type "Accessories"
        And the store has "LG G3", "LG headphones" and "LG earphones" products
        And I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Changing associated products of a product association
        Given the product "LG G3" has an association "Accessories" with product "LG headphones"
        When I want to modify the "LG G3" product
        And I associate "LG earphones" product as "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "LG G3" should have an association "Accessories" with products "LG headphones" and "LG earphones"

    @ui @javascript @todo
    Scenario: Removing an associated product of a product association
        Given the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        When I want to modify the "LG G3" product
        And I remove an associated product "LG earphones" from "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the product "LG G3" should have an association "Accessories" with product "LG headphones"
