@admin_dashboard
Feature: Displaying the notifications icon in the admin navbar
    In order to avoid seeing a non-functional icon
    As an Administrator
    I want the notifications icon in the navbar to be hidden when notifications are disabled

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Notifications icon is visible when notifications are enabled
        When I open administration dashboard
        Then I should see the notifications icon in the navbar

    @ui @notifications_disabled
    Scenario: Notifications icon is hidden when notifications are disabled
        Given the admin notifications are disabled
        When I open administration dashboard
        Then I should not see the notifications icon in the navbar
