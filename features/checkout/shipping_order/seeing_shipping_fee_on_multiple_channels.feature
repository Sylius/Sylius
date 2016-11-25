@checkout
Feature: Seeing detailed shipping fee on multiple channels with different base currency
    In order to be aware of shipping fee applied for my shipment
    As a Customer
    I want to be able to see shipping fee in channel’s base currency

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And the store ships to "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store has "UPS" shipping method with "$15.00" fee per shipment for "Web-US" channel and "£12.00" for "Web-GB" channel
        And the store has "FedEx" shipping method with "$10.00" fee per unit for "Web-US" channel and "£8.00" for "Web-GB" channel
        And the store allows paying offline for all channels
        And the store has a product "PHP T-Shirt" priced at "$12.54" available in channel "Web-US" and channel "Web-GB"
        And I am a logged in customer

    @ui
    Scenario: Seeing the shipping fee per shipment on selecting method in a channel's base currency
        Given I changed my current channel to "Web-US"
        And I have product "PHP T-Shirt" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "$15.00"
        And I should see shipping method "FedEx" with fee "$10.00"

    @ui
    Scenario: Seeing the shipping fee on selecting shipping method on a different channel in its base currency
        Given I changed my current channel to "Web-GB"
        And I have added 2 products "PHP T-Shirt" in the cart
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        Then I should be on the checkout shipping step
        And I should see shipping method "UPS" with fee "£12.00"
        And I should see shipping method "FedEx" with fee "£16.00"
