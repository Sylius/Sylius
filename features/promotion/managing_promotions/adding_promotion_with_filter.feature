@managing_promotions
Feature: Adding promotion with filter
    In order to give possibility to define which product should be affected by promotion
    As an Administrator
    I want to add a new promotion with filtered action to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a promotion with item fixed discount only for products over 10
        When I want to create a new promotion
        And I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for all products over $10!"
        And I add the "Item fixed discount" action configured with amount of "$10" for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price greater then "$10"
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for all products over $10!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with item fixed discount only for products between 10 and 100
        When I want to create a new promotion
        And I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for (almost) all products!"
        And I add the "Item fixed discount" action configured with amount of "$10" for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price between "$10" and "$100"
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for (almost) all products!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with fixed discount for all t-shirts
        Given the store classifies its products as "T-Shirts" and "Mugs"
        When I want to create a new promotion
        And I specify its code as "10_for_all_t_shirts"
        And I name it "$10 discount for all T-Shirts!"
        And I add the "Item fixed discount" action configured with amount of "$10" for "United States" channel
        And I specify that this action should be applied to items from "T-Shirt" category
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for all T-Shirts!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with fixed discount for PHP T-Shirt
        Given the store has a product "PHP T-Shirt" priced at "$100.00"
        When I want to create a new promotion
        And I specify its code as "10_for_php_t_shirt"
        And I name it "$10 discount for PHP T-Shirts!"
        And I add the "Item fixed discount" action configured with amount of "$10" for "United States" channel
        And I specify that this action should be applied to the "PHP T-Shirt" product
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for PHP T-Shirts!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with item percentage discount only for products over 10
        When I want to create a new promotion
        And I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for all products over $10!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price greater then "$10"
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for all products over $10!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with item percentage discount only for products between 10 and 100
        When I want to create a new promotion
        And I specify its code as "10_for_all_products_over_10"
        And I name it "$10 discount for (almost) all products!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that on "United States" channel this action should be applied to items with price between "$10" and "$100"
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for (almost) all products!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with 10% percentage discount for all t-shirts
        Given the store classifies its products as "T-Shirts" and "Mugs"
        When I want to create a new promotion
        And I specify its code as "10_for_all_t_shirts"
        And I name it "$10 discount for all T-Shirts!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that this action should be applied to items from "T-Shirt" category
        And I add it
        Then I should be notified that it has been successfully created
        And the "$10 discount for all T-Shirts!" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a promotion with 10% percentage discount for PHP T-Shirt
        Given the store has a product "PHP T-Shirt" priced at "$100.00"
        When I want to create a new promotion
        And I specify its code as "10_for_php_t_shirt"
        And I name it "10% discount for PHP T-Shirts!"
        And I add the "Item percentage discount" action configured with a percentage value of 10% for "United States" channel
        And I specify that this action should be applied to the "PHP T-Shirt" product
        And I add it
        Then I should be notified that it has been successfully created
        And the "10% discount for PHP T-Shirts!" promotion should appear in the registry
