@managing_promotions
Feature: Archiving promotions
    In order to hide no longer available promotions from the list and customers' use
    As an Administrator
    I want to archive promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And there is also a promotion "New Year sale"
        And I am logged in as an administrator

    @api @ui
    Scenario: Archiving a promotion
        When I browse promotions
        And I archive the "Christmas sale" promotion
        Then I should see a single promotion in the list
        And I should see the promotion "New Year sale" in the list

    @domain
    Scenario: Archiving a promotion does not remove it from the database
        When I archive the "Christmas sale" promotion
        Then the promotion "Christmas sale" should still exist in the registry

    @api @ui
    Scenario: Seeing only archived promotions
        Given the promotion "Christmas sale" is archived
        When I browse promotions
        And I filter archival promotions
        Then I should see a single promotion in the list
        And I should see the promotion "Christmas sale" in the list
        And I should not see the promotion "New Year sale" in the list

    @api @ui
    Scenario: Restoring an archival promotion
        Given the promotion "Christmas sale" is archived
        When I browse promotions
        And I filter archival promotions
        And I restore the "Christmas sale" promotion
        Then I should be viewing non archival promotions
        And I should see 2 promotions on the list
