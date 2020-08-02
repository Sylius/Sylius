@managing_tax_categories
Feature: Deleting multiple tax categories
    In order to remove test, obsolete or incorrect tax categories in an efficient way
    As an Administrator
    I want to be able to delete multiple tax categories at once

    Background:
        Given the store has tax categories "Alcohol", "Food" and "Books"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple tax categories at once
        When I browse tax categories
        And I check the "Alcohol" tax category
        And I check also the "Food" tax category
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single tax category in the list
        And I should see the tax category "Books" in the list
