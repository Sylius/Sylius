@managing_product_association_types
Feature: Sorting product association types
    In order to change the order of product association types
    As an Administrator
    I want to be able to sort product association types on the list

    Background:
        Given the store has a product association type "Cross sell" with a code "cross_sell"
        And the store has also a product association type "Up sell" with a code "up_sell"
        And I am logged in as an administrator

    @api @ui
    Scenario: Product associations can be sorted by code in ascending order
        Given I browse product association types
        When I sort the product associations ascending by code
        Then I should see 2 product association types in the list
        And the first product association on the list should have code "cross_sell"
        And the last product association on the list should have code "up_sell"

    @api @ui
    Scenario: Product associations can be sorted by code in descending order
        Given I browse product association types
        When I sort the product associations descending by code
        Then I should see 2 product association types in the list
        And the first product association on the list should have code "up_sell"
        And the last product association on the list should have code "cross_sell"
