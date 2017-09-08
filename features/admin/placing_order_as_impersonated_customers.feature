@impersonating_customers
Feature: Placing an order as impersonated shop users
    In order to provide a customer support
    As an Administrator
    I want to be able to place an order as an impersonated shop user

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "John Doe" identified by an email "john.doe@london.uk" and a password "johndoe"
        And there is a customer "Tanith Low" identified by an email "tanith.low@london.uk" and a password "tanithlow"
        And I am logged in as an administrator

    @ui
    Scenario: Placing an order as an impersonated shop user
        When I view details of the customer "tanith.low@london.uk"
        And I impersonate them
        And I visit the store
        And I add product "PHP T-Shirt" to the cart
        And I proceed through checkout process
        And I confirm my order
        And I browse orders of a customer "tanith.low@london.uk"
        Then I should see a single order in the list

    @ui
    Scenario: Placing an order as an impersonated shop user when the shop user was already logged
        When I sign in with email "john.doe@london.uk" and password "johndoe"
        And I view details of the customer "tanith.low@london.uk"
        And I impersonate them
        And I visit the store
        And I add product "PHP T-Shirt" to the cart
        And I proceed through checkout process
        And I confirm my order
        And I browse orders of a customer "tanith.low@london.uk"
        Then I should see a single order in the list
