@managing_promotions
Feature: Adding promotion with filter
    In order to give possibility to pay less for some goods based on specific configuration
    As an Administrator
    I want to add a new promotion with filtered action to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a promotion with item fixed discount only for products over 100
        Given I want to create a new promotion
        When I specify its code as "10_for_all_products_over_100"
        And I name it "$10 discount for all products over 100!"
        And I add the "Item fixed discount" action configured with amount of "$10"
        And I add a price filter with minimum value of "$10"
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for all products over 100!" promotion should appear in the registry

    @ui @javascript @skip
    Scenario: Adding a promotion with item fixed discount only for products between 10 and 100

    @ui @javascript @skip
    Scenario: Adding a promotion with item fixed discount only for products from a certain taxon

    @ui @javascript @skip
    Scenario: Adding a promotion with item percentage discount only for products over 100

    @ui @javascript @skip
    Scenario: Adding a promotion with item percentage discount only for products between 10 and 100

    @ui @javascript @skip
    Scenario: Adding a promotion with item percentage discount only for products from a certain taxon

    @ui @javascript @skip
    Scenario: Adding a promotion with item percentage discount only for products over 100 and from a certain taxon

    @ui @javascript @skip
    Scenario: Adding a promotion with item percentage discount only for products between 10 and 100 and from a certain taxon

    @ui @javascript @skip
    Scenario: Adding a promotion with fixed discount does not show filters

    @ui @javascript @skip
    Scenario: Adding a promotion with percentage discount does not show filters

