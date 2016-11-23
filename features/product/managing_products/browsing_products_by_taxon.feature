@managing_products
Feature: Browsing products
    In order to manage my shop merchandise
    As an Administrator
    I want to be able to browse products

    Background:
        Given the store classifies its products as "Mugs" and "Pugs"
        And the store has a product "Young pug"
        And this product belongs to "Pugs"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing all products
        Given the store has a product "Doge mascot"
        When I am browsing products
        Then I should see a product with name "Young pug"
        And I should see a product with name "Doge mascot"

    @ui
    Scenario: Browsing only products from specified category
        Given I am browsing products
        And the store has a product "Colorful mug"
        And this product belongs to "Mugs"
        When I filter them by "Pugs" taxon
        Then I should see a product with name "Young pug"
        But I should not see any product with name "Colorful mug"
