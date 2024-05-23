@managing_products
Feature: Filtering products by taxon
    In order to see only products from a specific category
    As an Administrator
    I want to filter products by taxon

    Background:
        Given the store classifies its products as "Mugs" and "Pugs"
        And the store has a product "Young pug"
        And this product belongs to "Pugs"
        And the store has a product "Colorful mug"
        And this product belongs to "Mugs"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api-todo
    Scenario: Filtering products by taxon
        Given I am browsing products
        When I filter them by "Pugs" taxon
        Then I should see a product with name "Young pug"
        But I should not see any product with name "Colorful mug"
