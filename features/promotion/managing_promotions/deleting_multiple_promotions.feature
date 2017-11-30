@managing_promotions
Feature: Deleting multiple promotions
    In order to remove test, obsolete or incorrect promotions in an efficient way
    As an Administrator
    I want to be able to delete multiple promotions at once from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And there is also a promotion "New Year sale"
        And there is also a promotion "Easter sale"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple promotions at once
        When I browse promotions
        And I check the "Christmas sale" promotion
        And I check also the "New Year sale" promotion
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single promotion in the list
        And I should see the promotion "Easter sale" in the list
