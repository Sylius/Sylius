@shop_error_page
Feature: Seeing not found page in the shop
    In order to provide a better user experience
    As a Visitor
    I want to see a not found page dedicated to the shop

    Background:
        Given the store operates on a channel named "Real Madrid"
        And the store has a product "Kroos T-Shirt"
        And the store has a product "Bellingham T-Shirt"

    @todo @no-api @ui
    Scenario: Seeing not found page when the product does not exist in the shop
        When I try to reach nonexistent product
        Then I should see the not found page
