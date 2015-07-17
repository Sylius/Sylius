@backend
Feature: Store dashboard
    In order to have an overview of my business
    As a store owner
    I need to be able to see sales info in backend dashboard

    Background:
        Given there is default currency configured
          And there is default channel configured
          And I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | French lands | country | French, Austria, Switzerland  |
          And there are products:
            | name          | price |
            | Mug           | 5.99  |
          And the following orders were placed:
            | customer               | address                                                |
            | julien@meetserious.com | Julien Boyer, 14 Rue Mandar, 75002, Paris, French       |
            | alpha@gmail.com        | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
        And order #000000001 has following items:
            | product | quantity |
            | Mug  | 5        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug  | 2        |

    Scenario: Viewing the dashboard logged in root
       Given I am on the dashboard
        Then I should see "Tableau de bord"
#
#    Scenario: Viewing recent orders
#        Given I am on the dashboard page
#         Then I should see 2 orders in the list
#
#    Scenario: Viewing recent customers
#        Given I am on the dashboard page
#         Then I should see 3 customers in the list
