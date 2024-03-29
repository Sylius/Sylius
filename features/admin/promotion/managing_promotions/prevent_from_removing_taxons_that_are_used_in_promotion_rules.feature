@managing_promotions
Feature: Preventing from removing taxons that are used in promotion rules
    In order to maintain integrity of promotions
    As an Administrator
    I want to be prevented from removing taxons that are used in promotion rules

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Mugs" taxonomy
        And I am logged in as an administrator

    @api @ui @mink:chromedriver
    Scenario: Being prevented from removing taxon that is in use by a promotion rule
        Given there is a promotion "Christmas sale" with "Total price of items from taxon" rule configured with "Mugs" taxon and $100 amount for "United States" channel
        When I try to delete taxon named "Mugs"
        Then I should be notified that this taxon could not be deleted as it is in use by a promotion rule
        And this taxon should still exist
