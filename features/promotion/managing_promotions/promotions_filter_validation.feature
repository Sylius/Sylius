@managing_promotions
Feature: Promotion filters validation
    In order to avoid making mistakes when managing a promotion with filters
    As an Administrator
    I want to be prevented from defining filters without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a promotion with wrong minimum price on price range filter
        Given I want to create a new promotion
        When I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for all products over $10!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price greater then "$asdasd"
        And I try to add it
        Then I should be notified that a minimum value should be a numeric value
        And promotion with name "$10 discount for all products over $10!" should not be added

    @ui @javascript
    Scenario: Adding a promotion with wrong maximum price on price range filter
        Given I want to create a new promotion
        When I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for (almost) all products!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price lesser then "$asdasda"
        And I try to add it
        Then I should be notified that a maximum value should be a numeric value
        And promotion with name "$10 discount for (almost) all products!" should not be added
