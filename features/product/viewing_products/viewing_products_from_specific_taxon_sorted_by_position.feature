@viewing_products
Feature: Viewing products from specific taxon sorted by position
    In order to see product in order chosen by shop owner
    As a Visitor
    I want to see sorted products

    Background:
        And the store operates on a channel named "Poland"
        And the store classifies its products as "Toys"
        And the "Toys" taxon has child taxon "Soft Toys"
        And the store has a product "Old pug"
        And this product is in "Toys" taxon at 1st position
        And this product is in "Soft Toys" taxon at 3rd position
        And the store has a product "Small pug"
        And this product is in "Toys" taxon at 3rd position
        And this product is in "Soft Toys" taxon at 1st position
        And the store has a product "Young pug"
        And this product is in "Toys" taxon at 2nd position
        And this product is in "Soft Toys" taxon at 2nd position

    @api @ui
    Scenario: Seeing sorted product on list from a specific taxon code
        When I browse products from product taxon code "Toys"
        Then I should see 3 products in the list
        And they should have order like "Old pug", "Young pug" and "Small pug"

    @ui @api
    Scenario: Seeing sorted product on list from a child taxon code
        When I browse products from product taxon code "Soft Toys"
        Then I should see 3 products in the list
        And they should have order like "Small pug", "Young pug" and "Old pug"

    @api @ui
    Scenario: Seeing sorted product on list from a child taxon
        When I browse products from taxon "Soft Toys"
        Then I should see 3 products in the list
        And they should have order like "Small pug", "Young pug" and "Old pug"
