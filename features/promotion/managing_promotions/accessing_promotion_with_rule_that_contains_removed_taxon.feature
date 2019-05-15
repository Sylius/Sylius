@managing_promotions
Feature: Accessing an edit page of a promotion with a rule that contains a removed taxon
    In order to change promotion details
    As an Administrator
    I want to be able to access an edit page of a promotion with a rule that contains a removed taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Accessing an edit page of a promotion with a rule that contains a removed taxon
        Given there is a promotion "Christmas sale" with "Has at least one from taxons" rule configured with "T-Shirts" and "Mugs"
        When I remove taxon named "Mugs"
        Then I should be notified that "Christmas sale" promotion has been updated
        And I should be able to modify a "Christmas sale" promotion
        And the "Christmas sale" promotion should have "Has at least one from taxons" rule configured

    @ui @javascript
    Scenario: Accessing an edit page of a promotion with a rule that contains a removed taxon
        Given there is a promotion "Christmas sale" with "Total price of items from taxon" rule configured with "Mugs" taxon and $100 amount for "United States" channel
        When I remove taxon named "Mugs"
        Then I should be notified that "Christmas sale" promotion has been updated
        And I should be able to modify a "Christmas sale" promotion
        And the "Christmas sale" promotion should not have any rule configured
