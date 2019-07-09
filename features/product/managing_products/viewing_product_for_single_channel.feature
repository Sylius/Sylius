@managing_products
Feature: Viewing a product in a single channel
    In order to check a product in shop in a single channel it is available in
    As an Administrator
    I want to be able to view product show page in shop

    Background:
        Given the store operates on a channel named "United States" with hostname "goodcars.com"
        And the store classifies its products as "Cars" and "Equipment"
        And the store has a product "Bugatti" priced at "$20.00" in "United States" channel
        And I am logged in as an administrator

    @ui
    Scenario: Viewing product shop page in a single channel
        Given I am browsing products
        When I access "Bugatti" product edit page
        And I show this product in this channel
        Then I should see this product in the "United States" channel in shop
