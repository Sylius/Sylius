@checkout
Feature: Leaving additional request notes on my order during checkout
    In for the administrators to know about my special requests
    As a Customer
    I want to be able to leave order notes for administrators to see

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is an administrator "sylius@example.com" identified by "sylius"
        And there is a customer account "customer@example.com" identified by "sylius"
        And I am logged in as "customer@example.com"

    @ui
    Scenario: Adding note on the checkout summary step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        When I provide additional note like "Code to the front gateway is #44*"
        And I confirm my order
        Then the administrator should know about this additional note for this order made by "customer@example.com"
