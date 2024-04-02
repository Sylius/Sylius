@managing_products
Feature: Changing associations of an existing product
    In order to change associations of my product
    As an Administrator
    I want to be able to change associations of an existing product

    Background:
        Given the store has a product association type "Accessories"
        And the store has "LG G3", "LG headphones" and "LG earphones" products
        And I am logged in as an administrator

    @ui @mink:chromedriver @no-api
    Scenario: Changing associated products of a product association
        Given the product "LG G3" has an association "Accessories" with product "LG headphones"
        When I want to modify the "LG G3" product
        And I associate as "Accessories" the "LG earphones" product
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an association "Accessories" with products "LG headphones" and "LG earphones"

    @api @no-ui
    Scenario: Adding another product to a product association
        Given the product "LG G3" has an association "Accessories" with product "LG headphones"
        When I add the product "LG earphones" to this product association
        Then this association should have products "LG headphones" and "LG earphones"

    @api @no-ui
    Scenario: Changing a product of a product association
        Given the product "LG G3" has an association "Accessories" with product "LG headphones"
        When I change this product association's product to the "LG earphones" product
        Then this association should only have product "LG earphones"

    @ui @mink:chromedriver @no-api
    Scenario: Removing an associated product of a product association
        Given the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        When I want to modify the "LG G3" product
        And I remove an associated product "LG earphones" from "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product should have an association "Accessories" with product "LG headphones"
        And this product should not have an association "Accessories" with product "LG earphones"

    @api @no-ui
    Scenario: Removing a product of a product association
        Given the product "LG G3" has an association "Accessories" with products "LG headphones" and "LG earphones"
        When I remove the product "LG earphones" from this product association
        Then this association should only have product "LG headphones"
