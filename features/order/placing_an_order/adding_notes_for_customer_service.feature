@placing_orders
Feature: Adding a note to order
    In order to provide some extra information to order
    As a Customer
    I want to be able to provide a note for customer service

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is an administrator identified by "sylius@example.com"
        And there is a customer account "customer@example.com" identified by "sylius"
        And I am logged in as "customer@example.com"

    @ui
    Scenario: Adding note on the checkout summary step
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        And I proceed order with "Free" shipping method and "Offline" payment
        When I provide additional note like "Code to the front gateway is #44*"
        And I confirm my order
        Then the administrator should know about this additional note for this order made by "customer@example.com"
