@managing_promotions
Feature: Preventing from removing products that are used in promotion rules
    In order to maintain integrity of promotions
    As an Administrator
    I want to be prevented from removing products that are used in promotion rules

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Mug" and "Cup" products
        And I am logged in as an administrator

    @api @ui
    Scenario: Being prevented from removing a product that is in use by a promotion rule
        Given there is a promotion "Christmas sale" with "Contains product" rule with product "Mug"
        When I try to delete the "Mug" product
        Then I should be notified that this product could not be deleted as it is in use by a promotion rule
        And this product should still exist
