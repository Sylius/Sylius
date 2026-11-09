@worker @worker:customer @mink:workerphp
Feature: Customer isolation between consecutive requests
    In order to see my own account and nobody else's
    As a Customer
    I want the shop to forget the previous customer before it serves me

    One process serves both customers in turn. A service that remembers the
    first one - a customer context, a cached token, a saved address - hands
    them to the second. The assertion is therefore after the SECOND sign-in,
    in the same scenario: two scenarios would never share a process.

    Background:
        Given the store operates on a single channel in the "United States" named "United States"
        And there is a customer account "robb@stark.com" identified by "KingInTheNorth"
        And there is a customer account "eddard@stark.com" identified by "handOfTheKing"

    @no-api @ui
    Scenario: A customer signing in after another one sees only their own profile
        Given I am logged in as "robb@stark.com"
        When I browse the "United States" channel
        And I want to modify my profile
        Then my email should be "robb@stark.com"
        When I log out
        And I sign in with email "eddard@stark.com" and password "handOfTheKing"
        And I want to modify my profile
        Then my email should be "eddard@stark.com"
