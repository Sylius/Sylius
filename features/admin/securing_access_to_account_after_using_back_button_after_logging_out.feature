@admin_dashboard
Feature: Securing access to the administration panel after using the back button after logging out
    In order to have administration panel secured
    As an Administrator
    I want to be unable to access to the administration panel by using the back button after logging out

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @mink:chromedriver @no-api
    Scenario: Securing access to administration dashboard after using the back button after logging out
        When I am on the administration dashboard
        And I log out
        And I go back one page in the browser
        Then I should not see the administration dashboard
        And I should be on the login page
