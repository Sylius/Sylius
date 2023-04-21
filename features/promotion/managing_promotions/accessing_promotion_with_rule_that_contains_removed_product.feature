@managing_promotions
Feature: Accessing an edit page of a promotion with a rule that contains a removed product
    In order to change promotion details
    As an Administrator
    I want to be able to access an edit page of a promotion with a rule that contains a removed product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Mug" and "Cup" products
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Accessing an edit page of a promotion with a rule that contains the removed product
        Given there is a promotion "Christmas sale" with "Contains product" rule with products "Mug" and "Cup"
        When I delete the "Cup" product
        Then I should be notified that "Christmas sale" promotion has been updated
        And I should be able to modify a "Christmas sale" promotion
        And the "Christmas sale" promotion should have "Contains product" rule configured

    @ui @no-api
    Scenario: Accessing an edit page of a promotion with a rule that contains only the removed product
        Given there is a promotion "Christmas sale" with "Contains product" rule with product "Mug"
        When I delete the "Mug" product
        Then I should be notified that "Christmas sale" promotion has been updated
        And I should be able to modify a "Christmas sale" promotion
        And the "Christmas sale" promotion should not have any rule configured
