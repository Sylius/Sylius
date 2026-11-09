@worker @worker:locale @mink:workerphp
Feature: Locale isolation between consecutive requests
    In order to read the shop in its own language
    As a Customer
    I want the locale of the channel served before me not to stick

    The locale context resolves the list of available locales per request. In
    worker mode the service outlives the request, so a list remembered on first
    use is negotiated against for every channel that follows. Both visits live
    in ONE scenario on purpose - the leak needs a predecessor.

    Background:
        Given the store operates on a channel named "Web" with hostname "web.example"
        And that channel allows to shop using "English (United States)", "Polish (Poland)" and "Norwegian (Norway)" locales
        And it uses the "English (United States)" locale by default
        And the store operates on another channel named "Mobile" with hostname "mobile.example"
        And that channel allows to shop using "Polish (Poland)" and "Norwegian (Norway)" locales
        And it uses the "Polish (Poland)" locale by default

    @no-api @ui
    Scenario: A channel served after another one offers only its own locales
        When I browse the "Web" channel
        Then I should shop using the "English (United States)" locale
        When I browse the "Mobile" channel
        Then I should shop using the "Polish (Poland)" locale
        And I should not be able to shop using the "English (United States)" locale

    @no-api @ui
    Scenario: The first channel keeps its own locale after the second one was served
        When I browse the "Mobile" channel
        Then I should shop using the "Polish (Poland)" locale
        When I browse the "Web" channel
        Then I should shop using the "English (United States)" locale
        And I should be able to shop using the "Norwegian (Norway)" locale
