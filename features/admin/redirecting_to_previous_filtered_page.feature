@admin_panel
Feature: Redirecting to previous filtered page
    In order to have proper filtered page
    As an Administrator
    I want to be able to redirect to previous filtered page after any action from index

    Background:
        Given the store operates on a channel named "Poland"
        And the store classifies its products as "Clothes"
        And the store has a product "FC Barcelona T-Shirt"
        And the store has a product "FC Barcelona Home T-Shirt"
        And I am logged in as an administrator

    @ui
    Scenario: Redirecting to previous filtered page after delete product
        When I browse products
        And I choose enabled filter
        And I filter
        And I delete the "FC Barcelona T-Shirt" product on filtered page
        Then I should be redirected to the previous filtered page with enabled filter

    @ui
    Scenario: Redirecting to previous filtered page after cancelling editing product
        When I browse products
        And I choose enabled filter
        And I filter
        And I want to modify the "FC Barcelona T-Shirt" product
        And I cancel my changes
        Then I should be redirected to the previous filtered page with enabled filter

    @ui @javascript
    Scenario: Redirecting to previous filtered page after cancelling editing product after creating new product
        When I browse products
        And I choose enabled filter
        And I filter
        And I create a new simple product "FC Barcelona Away T-Shirt" priced at "$20.00" with "Clothes" taxon in the "Poland" channel
        And I cancel my changes
        Then I should be redirected to the previous filtered page with enabled filter
