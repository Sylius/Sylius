@managing_product_association_types
Feature: Browsing product association types
    In order to see all product association types in the store
    As an Administrator
    I want to browse product association types

    Background:
        Given the store has a product association type "Cross sell"
        And the store has also a product association type "Up sell"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing product association types in the store
        When I want to browse product association types
        Then I should see 2 product association types in the list
        And I should see the product association type "Cross sell" in the list
