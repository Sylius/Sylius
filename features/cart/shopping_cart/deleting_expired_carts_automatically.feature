@shopping_cart
Feature: Deleting expired carts automatically
    In order to remove started but not finished orders after 2 days of idleness
    As an Administrator
    I want to have expired carts automatically deleted

    Background:
        Given the store operates on a single channel in "United States"

    @domain
    Scenario: Having cart deleted after 4 days of idleness
        Given a customer "john.doe@gmail.com" added something to cart
        And he abandoned the cart 4 days ago
        Then this cart should be automatically deleted

    @domain
    Scenario: Having idle cart in registry if expiration time has not been reached
        Given a customer "john.doe@gmail.com" added something to cart
        And he abandoned the cart 1 day ago
        Then this cart should not be deleted
