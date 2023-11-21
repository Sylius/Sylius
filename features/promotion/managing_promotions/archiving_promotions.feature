@managing_promotions
Feature: Archiving promotions
    In order to hide no longer available promotions from the list and customers' use
    As an Administrator
    I want to archive promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Basic promotion" and "Additional promotion"
        And I am logged in as an administrator

    @ui @api
    Scenario: Archiving a promotion
        When I browse promotions
        And I archive the "Basic promotion" promotion
        Then I should see a single promotion in the list
        And the "Additional promotion" promotion should be listed on the current page

    @domain
    Scenario: Archiving a promotion does not remove it from the database
        When I archive the "Basic promotion" promotion
        Then promotion "Basic promotion" should still exist in the registry

    @ui @api
    Scenario: Seeing only archived promotions
        Given the promotion "Basic promotion" is archival
        When I browse promotions
        And I filter archival promotions
        Then I should see a single promotion in the list
        And the "Basic promotion" promotion should be listed on the current page
        And the "Additional promotion" promotion shouldn't be listed on the current page

    @ui @api
    Scenario: Restoring an archival promotion
        Given the promotion "Basic promotion" is archival
        When I browse promotions
        And I filter archival promotions
        And I restore the "Basic promotion" promotion
        Then I should be viewing non archival promotions
        And I should see 2 promotions on the list
