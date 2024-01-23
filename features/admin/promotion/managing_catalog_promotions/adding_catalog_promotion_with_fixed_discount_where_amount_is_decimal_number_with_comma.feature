@managing_catalog_promotions
Feature: Adding a new catalog promotion with fixed discount where amount is a decimal number with comma
    In order to be able to create catalog promotions with fixed discount where amount is a decimal number with comma
    As an Administrator
    I want to be able to add a new catalog promotion with fixed discount where amount is a decimal number with comma without errors

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @no-api @javascript
    Scenario: Adding a new catalog promotion with fixed discount and amount with comma
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add action that gives "$10,25" of fixed discount in the "United States" channel
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And the "Winter sale" catalog promotion should have "$10.25" of fixed discount in the "United States" channel
