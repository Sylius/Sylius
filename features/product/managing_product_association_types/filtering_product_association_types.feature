@managing_product_association_types
Feature: Filtering product association types
    In order to filter product association types
    As an Administrator
    I want to be able to filter product association types on the list

    Background:
        Given the store has a product association type "Cross sell" with a code "cross_sell"
        And the store has also a product association type "Up sell" with a code "up_sell"
        And I am logged in as an administrator

    @ui
    Scenario: Filtering product association types by name
        When I want to browse product association types
        And I filter product association types with name containing "Up"
        Then I should see only one product association type in the list
        And I should see the product association type "Up sell" in the list

    @ui
    Scenario: Filtering product association types by code
        When I want to browse product association types
        And I filter product association types with code containing "cross"
        Then I should see only one product association type in the list
        And I should see the product association type "Cross sell" in the list
