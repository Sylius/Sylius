@viewing_shipping_methods
Feature: Viewing available shipping methods based on channel as a Visitor
    In order to only see applicable shipping methods
    As a Visitor
    I want to see the shipping methods that are available to my order based on the channel

    Background:
        Given the store operates on a channel named "United Kingdom" in "USD" currency and with hostname "uk.cool-clothes.example"
        And the store operates on another channel named "United States" in "USD" currency and with hostname "usa.cool-clothes.example"
        And the store has a zone "World"
        And the store ships to "United States"
        And this zone has the "United States" country member
        And the store ships everywhere for free for all channels
        And the store has "ultra fast" shipping method with "$4.00" fee per unit for "United States" channel
        And the store has "uber speedy" shipping method with "$15.00" fee per shipment for "United Kingdom" channel
        And the store has a product "T-Shirt" priced at "$20.00" available in channel "United Kingdom" and channel "United States"

    @api @ui
    Scenario: Seeing shipping methods that are available in channel
        Given I changed my current channel to "United States"
        And I have product "T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        Then I should be on the checkout shipping step
        And I should see "ultra fast" shipping method
        And I should not see "uber speedy" shipping method

    @api @ui
    Scenario: Seeing shipping methods that are available in another channel
        Given I changed my current channel to "United Kingdom"
        And I have product "T-Shirt" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        Then I should be on the checkout shipping step
        And I should see "uber speedy" shipping method
        And I should not see "ultra fast" shipping method
