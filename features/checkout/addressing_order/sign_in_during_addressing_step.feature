@checkout
Feature: Sign in to the store during checkout
    In order to sign during the addressing step
    As a Guest
    I want to be able to sign in

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"

    @ui @javascript
    Scenario: Displaying login form if the customer has an account
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "francis@underwood.com"
        Then I should be able to log in

    @ui @javascript
    Scenario: Successful sign in
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "francis@underwood.com"
        And I specify the password as "whitehouse"
        And I sign in
        Then the login form should no longer be accessible

    @ui @javascript
    Scenario: Failure sign in
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "francis@underwood.com"
        And I specify the password as "francis"
        And I sign in
        Then I should be notified about bad credentials
