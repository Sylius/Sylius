@channels
Feature: Resolving channel by hostname
    In order to sell through different entry points
    As a developer
    I want to resolve channels by hostname

    Background:
        Given store has default configuration
          And there are following currencies configured:
            | code |
            | USD  |
            | EUR  |
          And there are following locales configured:
            | code  |
            | en_US |
            | fr_FR |
            | de_DE |
          And I am logged in as user

#    Scenario: Trying to view homepage without a default channel configured
#        When I go to the website root
#        Then I should see "The channel could not have been correctly resolved"

    Scenario: Resolve channel by hostname
      Given there are following channels configured:
        | code   | name          | currencies | locales      | url            |
        | WEB-US | mystore.us    | USD        | en_US        | www.mystore.us |
        | WEB-EU | mystore.eu    | EUR        | de_DE        | www.mystore.eu |
        | WEB-FR | mystore.fr    | EUR        | fr_FR        | www.mystore.fr |
      And I am on the "fr" hostname
      Then I should be on the "WEB-FR" channel



