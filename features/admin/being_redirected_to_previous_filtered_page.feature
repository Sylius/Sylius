@admin_panel
Feature: Being redirected to previous filtered page
    In order to return to a properly filtered page
    As an Administrator
    I want to be redirected to a previously filtered page after taking any action on index

    Background:
        Given the store operates on a channel named "Poland"
        And the store classifies its products as "Clothes"
        And the store has 15 products
        And the store has a product "FC Barcelona T-Shirt"
        And the store has a product "Znicz Pruszków T-Shirt"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Being redirected to previous filtered page after deleting a product
        When I browse products
        And I choose enabled filter
        And I filter
        And I delete the "FC Barcelona T-Shirt" product on filtered page
        Then I should be redirected to the previous page of only enabled products

    @ui @no-api
    Scenario: Being redirected to previous filtered page after cancelling editing a product
        When I browse products
        And I choose enabled filter
        And I filter
        And I want to modify the "FC Barcelona T-Shirt" product
        And I cancel my changes
        Then I should be redirected to the previous page of only enabled products

    @ui @no-api
    Scenario: Being redirected to previous filtered page with pagination after cancelling editing a product
        When I browse products
        And I choose enabled filter
        And I filter
        And I go to the 2nd page
        And I want to modify the "Znicz Pruszków T-Shirt" product
        And I cancel my changes
        Then I should be redirected to the 2nd page of only enabled products

    @ui @no-api
    Scenario: Being redirected to previous filtered page after cancelling creating a new product
        When I browse products
        And I choose enabled filter
        And I filter
        And I go to the 2nd page
        And I want to create a new simple product
        And I cancel my changes
        Then I should be redirected to the 2nd page of only enabled products
