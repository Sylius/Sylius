@managing_products
Feature: Viewing a product in a chosen channel
    In order to check a product in shop in all channels it is available in
    As an Administrator
    I want to choose a channel in which I will be viewing it

    Background:
        Given the store operates on a channel named "United States" with hostname "goodcars.com"
        And the store also operates on a channel named "Europe" with hostname "goodcars.eu"
        And the store classifies its products as "Cars" and "Equipment"
        And the store has a product "Bugatti" priced at "$20.00" in "United States" channel
        And this product is available in the "Europe" channel
        And I am logged in as an administrator

    @ui
    Scenario: Viewing product shop page in a chosen channel
        Given I am browsing products
        When I access "Bugatti" product edit page
        And I show this product in the "Europe" channel
        Then I should see this product in the "Europe" channel in shop
