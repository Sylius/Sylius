@shopping_cart
Feature: Deleting expired carts automatically
    In order to get rid of started but not finished orders
    As an Administrator
    I want to have expired carts automatically deleted

    Background:
        Given the store operates on a single channel in "United States"

    @domain
    Scenario:
        Given a customer "john.doe@gmail.com" started checkout
        And he abandoned the cart 2 hours ago
        Then this cart should be deleted from registry
