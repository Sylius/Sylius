@viewing_products
Feature: Sorting listed products from a taxon by position
    In order to change the order by which products from a taxon are displayed
    As an Administrator
    I want to sort products from a taxon by their positions

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store classifies its products as "Soft Toys"
        And the store has a product "Old pug"
        And this product is in "Soft Toys" taxon at 1st position
        And the store has a product "Small pug"
        And this product is in "Soft Toys" taxon at 3rd position
        And the store has a product "Young pug"
        And this product is in "Soft Toys" taxon at 2nd position
        And I am a logged in customer

    @ui
    Scenario: Seeing sorted product on list from a specific taxon
        When I browse products from taxon "Soft Toys"
        Then I should see 3 products in the list
        And they should have order like "Old pug", "Young pug" and "Small pug"
