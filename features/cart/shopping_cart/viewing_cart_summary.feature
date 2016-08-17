@shopping_cart
Feature: Viewing a cart summary
    In order to see details about my order
    As a visitor
    I want to be able to see my cart summary

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing information about empty cart
        When I see the summary of my cart
        Then my cart should be empty
