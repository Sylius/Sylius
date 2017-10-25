@managing_shipping_categories
Feature: Deleting multiple shipping categories
    In order to remove test, obsolete or incorrect shipping categories in an efficient way
    As an Administrator
    I want to be able to delete multiple shipping categories at once

    Background:
        Given the store has "Standard" shipping category
        And the store has "Big" and "Small" shipping category
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple shipping categories at once
        When I browse shipping categories
        And I check the "Big" shipping category
        And I check also the "Small" shipping category
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single shipping category in the list
        And I should see the shipping category "Standard" in the list
