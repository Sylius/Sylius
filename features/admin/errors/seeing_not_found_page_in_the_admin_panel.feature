@admin_error_page
Feature: Seeing not found page in the admin panel
    In order to provide a better user experience
    As an administrator
    I want to see a not found page dedicated to the admin panel

    Background:
        Given the store operates on a channel named "Real Madrid"
        And the store has a product "Kroos T-Shirt"
        And the store has a product "Bellingham T-Shirt"
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Seeing not found page when the product does not exist in the admin panel
        When I try to reach nonexistent product
        Then I should see the not found page with the link to the dashboard
