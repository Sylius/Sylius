@admin_dashboard
Feature: Redirect when already signed in
    In order to be aware that I am already logged in
    As an Administrator
    I want to be redirected to the administration dashboard by using when accessing the login page

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Trying to access login page as logged in administrator
        When I want to log in
        Then I should be redirected to the administration dashboard

