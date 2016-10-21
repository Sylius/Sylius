@managing_products
Feature: Browsing products
    In order to manage my shop merchandise
    As an Administrator
    I want to be able to browse products

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "Mugs" and "Pugs"
        And the store has a product "Colorful mug"
        And this product belongs to "Mugs"
        And the store has a product "Young pug"
        And this product belongs to "Pugs"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing defined products
        Given I am browsing products
        When I filter them by "Pugs" taxon
        Then I should see a product with name "Young pug"
        But I should not see any product with name "Colorful mug"
