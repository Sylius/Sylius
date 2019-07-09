@managing_product_variants
Feature: Viewing a product in a single channel
    In order to check a product in shop in a single channel it is available in
    As an Administrator
    I want to be able to view product show page in shop

    Background:
        Given the store operates on a channel named "United States" with hostname "goodcars.com"
        And the store classifies its products as "Cars" and "Equipment"
        And the store has a product "Bugatti" priced at "$200000.00" in "United States" channel
        And the product "Bugatti" has "Red" variant priced at "$220000.00"
        And I am logged in as an administrator

    @ui
    Scenario: Viewing configurable product shop page in a single channel
        Given I am browsing "Bugatti" product variants
        When I access "Red" variant edit page
        And I show this product in this channel
        Then I should see this product in the "United States" channel in shop
