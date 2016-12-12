@checkout
Feature: Assigning customer's IP address to a placed order
    In order to know from which IP address a new order has been places
    As an Administrator
    I want to have customer's IP address assigned to their orders
    
    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is an administrator "sylius@example.com" identified by "sylius"
        And there is a customer account "customer@example.com" identified by "sylius"
        And I am logged in as "customer@example.com"

    @ui
    Scenario: Assigning customer's IP address to a newly placed order
        Given I have product "PHP T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        Then the administrator should know about IP address of this order made by "customer@example.com"
