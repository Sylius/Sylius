@showing_available_plugins @cli
Feature: Showing available Sylius plugins
    In order to be aware of available plugins
    As a Developer
    I want to be informed about available plugins

    Scenario: Showing available Sylius plugins
        When I run show available plugins command
        Then I should see output "Available official plugins and selected community plugins" with listed plugins
