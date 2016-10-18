@viewing_products
Feature: Sorting listed products
    In order to change the order in which products are displayed
    As an Customer
    I want to be able to sort products

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Fluffy Pets"
        And the store has a product "Berserk Pug" created at "05-10-2016" and priced at "$12.51"
        And this product has "Small" variant priced at "$16.51"
        And this product belongs to "Fluffy Pets"
        And the store also has a product "Pug of Love" created at "06-10-2016" and priced at "$16.50"
        And this product has "Large" variant priced at "$15.50"
        And this product belongs to "Fluffy Pets"
        And the store also has a product "Xtreme Pug" created at "07-09-2016" and priced at "$15.00"
        And this product belongs to "Fluffy Pets"
        And this product has "Monster" variant priced at "$12.50"

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

    @ui
    Scenario: Sorting products by their prices with ascending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products from the lowest price
        Then I should see 3 products in the list
        And I should see a product with name "Berserk Pug"
        But the first product on the list should have name "Xtreme Pug"

    @ui
    Scenario: Sorting products by their prices with descending order
        When I browse products from taxon "Fluffy Pets"
        And I start sorting products from the highest price
        Then I should see 3 products in the list
        And I should see a product with name "Berserk Pug"
        But the first product on the list should have name "Pug of Love"
