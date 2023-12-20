@viewing_product_in_admin_panel
Feature: Checking products in the shop while viewing them
    In order to check a product in shop in all channels it is available in
    As an Administrator
    I want to choose a channel in which I will be viewing it

    Background:
        Given the store operates on a channel named "United States" with hostname "goodcars.com"
        And the store also operates on a channel named "Europe" with hostname "goodcars.eu"
        And the store has a product "Bugatti" available in "United States" channel
        And I am logged in as an administrator
        And I am browsing products

    @ui @no-api
    Scenario: Accessing product show page in shop from the product show page where product is available in more than one channel
        Given this product is available in the "Europe" channel
        When I access the "Bugatti" product
        And I show this product in the "Europe" channel
        Then I should see this product in the "Europe" channel in the shop

    @ui @no-api
    Scenario: Accessing product show page in shop from the product show page where product is available in one channel
        When I access the "Bugatti" product
        And I show this product in this channel
        Then I should see this product in the "Europe" channel in the shop

    @ui @no-api
    Scenario: Being unable to access product show page in shop when the product is disabled
        Given this product has been disabled
        When I access the "Bugatti" product
        Then I should not be able to show this product in shop
