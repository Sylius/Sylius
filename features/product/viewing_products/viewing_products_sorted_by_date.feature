@viewing_products
Feature: Sorting listed products
    In order to change the order in which products are displayed
    As an Customer
    I want to be able to sort products

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Fluffy Pets"
        And the store has a product "Berserk Pug" with code "B_PUG", created at "05-10-2016"
        And this product belongs to "Fluffy Pets"
        And the store also has a product "Pug of Love" with code "L_PUG", created at "06-10-2016"
        And this product belongs to "Fluffy Pets"
        And the store also has a product "Xtreme Pug" with code "X_PUG", created at "07-09-2016"
        And this product belongs to "Fluffy Pets"

    @ui
    Scenario: Sorting products by their dates with descending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products from the latest
        Then I should see 3 products in the list
        And I should see a product with name "Xtreme Pug"
        But the first product on the list should have name "Pug of Love"

    @ui
    Scenario: Sorting products by their dates with ascending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products from the oldest
        Then I should see 3 products in the list
        And I should see a product with name "Berserk Pug"
        But the first product on the list should have name "Xtreme Pug"

    @todo
    Scenario: Sorting products by their names with descending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products by name in descending order
        Then I should see 3 products in the list
        And I should see a product with name "Pug of Love"
        But the first product on the list should have name "Xtreme Pug"

    @todo
    Scenario: Sorting products by their names with ascending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products by name in ascending order
        Then I should see 3 products in the list
        And I should see a product with name "Pug of Love"
        But the first product on the list should have name "Berserk Pug"
