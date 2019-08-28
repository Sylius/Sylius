@checkout
Feature: After completing checkout being able to go on order in my account
    In order to managing my new order easily
    As a Customer
    I want to being able to go on order in my account after completing checkout

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store allows paying "Cash on delivery"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui
    Scenario: Being able to go on order in my account after completing checkout
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Cash on delivery" payment method
        When I have confirmed order
        Then I should be able to go to order details in my account
