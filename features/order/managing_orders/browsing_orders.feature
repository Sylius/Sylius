@managing_orders
Feature: Browsing orders
    In order to process orders
    As an Administrator
    I want to be able to browse new orders

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing a new order in the list
        When I browse orders
        Then I should see a single order from customer "john.doe@gmail.com"
