@shopping_cart
Feature: Deleting expired carts automatically
    In order to get rid of started but not finished orders
    As an Administrator
    I want to have expired carts automatically deleted

    Background:
        Given the store operates on a single channel in "United States"

    @domain
    Scenario: Having cart deleted after 3 hours of idleness
        Given a customer "john.doe@gmail.com" started checkout
        And he abandoned the cart 4 hours ago
        Then this cart should be deleted from registry

    @domain
    Scenario: Having idle cart in registry if expiration time has not been reached
        Given a customer "john.doe@gmail.com" started checkout
        And he abandoned the cart 1 hours ago
        Then this cart should be still in the registry
