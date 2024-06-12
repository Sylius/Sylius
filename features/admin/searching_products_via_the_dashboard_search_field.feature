@admin_dashboard
Feature: Searching products via the dashboard search field
    In order to search for products easily
    As an Administrator
    I want to be able to search for products via the dashboard search field

    Background:
        Given the store operates on a channel named "WEB-POLAND"
        And there is product "Onion" available in this channel
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Searching for a product via the dashboard search field
        When I open administration dashboard
        And I search for product "Onion"
        Then I should see a single product in the list
        And I should see a product with name "Onion"
