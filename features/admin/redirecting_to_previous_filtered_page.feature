@admin_dashboard
Feature: Redirecting to previous filtered page
    In order to have proper filtered page
    As an Administrator
    I want to be able to redirect to previous filtered page after any action from index

    Background:
        Given the store operates on a channel named "Poland"
        And I am logged in as an administrator
        And I am browsing products

    @ui
    Scenario: Redirecting to previous filtered page after delete product
        Given I have only disabled products filtered out
        And I am on page 3
        When I delete one of the products
        And I am returned to the products index
        Then I should be on page number 3 of the disabled products index

    @ui
    Scenario: Redirecting to previous filtered page after cancelling editing product
        Given I have only disabled products filtered out
        And I am on page 3
        When I edit one of the products
        And I cancel the edit
        And I am returned to the products index
        Then I should be on page number 3 of the disabled products index
