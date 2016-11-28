@managing_orders
Feature: Seeing orders' total in their currency
    In order to know how much, and in which currency a customer placed an order
    As an Administrator
    I want to be able to see orders' total in it's currency

    Background:
        Given the store operates on a channel named "United States" in "USD" currency
        And the store operates on another channel named "Great Britain" in "GBP" currency
        And the store ships to "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store ships everywhere for free for all channels
        And the store allows paying offline for all channels
        And the store has a product "Angel T-Shirt" priced at "$20.00" available in channel "United States" and channel "Great Britain"
        And there is an administrator "sylius@example.com" identified by "sylius"
        And there is a customer account "customer@example.com" identified by "sylius"
        And I am logged in as "customer@example.com"

    @ui
    Scenario: List of orders from only one channel
        Given I changed my current channel to "United States"
        And I have product "Angel T-Shirt" in the cart
        And I specified the shipping address as "Los Angeles", "Frost Alley", "90210", "United States" for "Lucifer Morningstar"
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        Then the administrator should see the order with total "$20.00" in order list

    @ui
    Scenario: List of orders from different channels
        Given I changed my current channel to "United States"
        And I have product "Angel T-Shirt" in the cart
        And I specified the shipping address as "Los Angeles", "Frost Alley", "90210", "United States" for "Lucifer Morningstar"
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        And I changed my current channel to "Great Britain"
        And I had product "Angel T-Shirt" in the cart
        And I specified the shipping address as "Los Angeles", "Frost Alley", "90210", "United States" for "Lucifer Morningstar"
        And I proceed with "Free" shipping method and "Offline" payment
        When I confirm my order
        Then the administrator should see the order with total "£20.00" in order list
        And the administrator should see the order with total "$20.00" in order list
