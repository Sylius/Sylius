@checkout
Feature: Registering a new account after checkout
    In order to make future purchases with ease
    As an Visitor
    I want to be able to create an account in the store after placing an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline

    @ui @no-api
    Scenario: Displaying thank you page after registration
        Given on this channel account verification is required
        And I have product "PHP T-Shirt" in the cart
        And I have completed addressing step with email "john@example.com" and "United States" based billing address
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I have confirmed order
        When I proceed to the registration
        And I specify a password as "sylius"
        And I confirm this password
        And I register this account
        Then I should be on registration thank you page

    @ui @no-api
    Scenario: Registering a new account after checkout when channel has enabled registration verification
        Given on this channel account verification is required
        And I have product "PHP T-Shirt" in the cart
        And I have completed addressing step with email "john@example.com" and "United States" based billing address
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I have confirmed order
        When I proceed to the registration
        And I specify a password as "sylius"
        And I confirm this password
        And I register this account
        And I verify my account using link sent to "john@example.com"
        Then I should be able to log in as "john@example.com" with "sylius" password

    @ui @no-api
    Scenario: Registering a new account after checkout when channel has disabled registration verification
        Given on this channel account verification is not required
        And I have product "PHP T-Shirt" in the cart
        And I have completed addressing step with email "john@example.com" and "United States" based billing address
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I have confirmed order
        When I proceed to the registration
        And I specify a password as "sylius"
        And I confirm this password
        And I register this account
        Then I should be on my account dashboard
