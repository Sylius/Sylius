@managing_products
Feature: Filtering products by taxons
    In order to see only products from a specific category
    As an Administrator
    I want to filter products by taxon

    Background:
        Given the store classifies its products as "Mugs" and "Pugs"
        And the store has a product "Young pug"
        And this product has a main taxon "Pugs"
        And this product belongs to "Pugs"
        And the store has a product "Old pug"
        And this product has a main taxon "Pugs"
        And this product belongs to "Pugs"
        And the store has a product "Colorful mug"
        And this product has a main taxon "Mugs"
        And this product belongs to "Mugs"
        And the store has a product "Colorful pug mug"
        And this product has a main taxon "Mugs"
        And this product belongs to "Pugs"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Filtering products by taxon
        Given I am browsing products
        When I filter them by "Pugs" taxon
        Then I should see 3 products in the list
        And I should not see any product with name "Colorful mug"

    @ui @mink:chromedriver @api-todo
    Scenario: Filtering products by main taxon
        Given I am browsing products
        When I filter them by "Pugs" main taxon
        Then I should see 2 products in the list
        And I should see a product with name "Young pug"
        And I should see a product with name "Old pug"
