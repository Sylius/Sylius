@managing_products
Feature: Sorting listed products from a taxon by position
    In order to change the order by which products from a taxon are displayed
    As an Administrator
    I want to sort products from a taxon by their positions

    Background:
        Given the store classifies its products as "Soft Toys"
        And the store has a product "Old pug"
        And this product is in "Soft Toys" taxon at 1st position
        And the store has a product "Young pug"
        And this product is in "Soft Toys" taxon at 2nd position
        And the store has a product "Small pug"
        And this product is in "Soft Toys" taxon at 3rd position
        And I am logged in as an administrator

    @ui
    Scenario: Products are sorted by position in ascending order by default
        When I am browsing products from "Soft Toys" taxon
        Then I should see 3 products in the list
        And they should have order like "Old pug", "Young pug" and "Small pug"

    @ui
    Scenario: New product is added as last one
        Given I added a product "Big pug"
        And I assigned this product to "Soft Toys" taxon
        When I am browsing products from "Soft Toys" taxon
        Then the last product on the list should have name "Big pug"

    @ui @javascript
    Scenario: Product with position 0 is set as the first one
        When I am browsing products from "Soft Toys" taxon
        And I set the position of "Young pug" to 0
        And I save my new configuration
        Then the first product on the list should have name "Young pug"

    @ui @javascript
    Scenario: Product with the highest position is set as the last one
        When I am browsing products from "Soft Toys" taxon
        And I set the position of "Young pug" to 12
        And I save my new configuration
        Then the last product on the list should have name "Young pug"

    @ui
    Scenario: Sort products in descending order
        When I am browsing products from "Soft Toys" taxon
        And I start sorting products by position
        Then I should see 3 products in the list
        And they should have order like "Small pug", "Young pug" and "Old pug"
