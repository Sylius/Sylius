@managing_products
Feature: Checking products in the shop while editing them
    In order to check a product in shop in all channels it is available in
    As an Administrator
    I want to choose a channel in which I will be viewing it

    Background:
        Given the store operates on a channel named "United States" with hostname "goodcars.com"
        And the store also operates on a channel named "Europe" with hostname "goodcars.eu"
        And the store has a product "Bugatti" priced at "$20.00" in "United States" channel
        And I am logged in as an administrator

    @ui
    Scenario: Accessing product show page in shop from the product edit page where product is available in more than one channel
        Given this product is available in the "Europe" channel
        And I am browsing products
        When I want to modify this product
        And I choose to show this product in the "Europe" channel
        Then I should see this product in the "Europe" channel in the shop

    @ui
    Scenario: Accessing product show page in shop from the product edit page where product is available in one channel
        Given I am browsing products
        When I want to modify this product
        And I choose to show this product in this channel
        Then I should see this product in the "United States" channel in the shop
