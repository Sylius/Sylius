@managing_administrators
Feature: Deleting multiple administrators
    In order to get rid of deprecated administrators in an efficient way
    As an Administrator
    I want to be able to delete multiple administrator accounts at once

    Background:
        Given there is an administrator "banana@example.com"
        And there is also an administrator "orange@example.com"
        And there is also an administrator "watermelon@example.com"
        And I am logged in as "watermelon@example.com" administrator

    @ui @javascript
    Scenario: Deleting multiple administrators at once
        Given I browse administrators
        And I check the "banana@example.com" administrator
        And I check also the "orange@example.com" administrator
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single administrator in the list
        And I should see the administrator "watermelon@example.com" in the list
