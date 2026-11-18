@worker @worker:theme @mink:workerphp
Feature: Theme isolation between consecutive requests
    In order to serve several channels from one long-lived process
    As a Developer
    I want a request to render its own channel's theme, not the one resolved before it

    ChannelBasedThemeContext resolves the theme once and keeps the answer in a
    property, so in worker mode it outlives the request that asked for it. Each
    scenario below is a SEQUENCE of requests inside one scenario, with the
    assertion after the second one - split across two scenarios it would measure
    nothing, because the leak needs a predecessor.

    One themed channel and one plain one, not two themed ones: the test theme
    repository loads its list once per process, so a second theme added later in
    the same Background is never found.

    For the same reason this suite has to run before any other one touches the
    shop on the same worker. The list is loaded lazily, on the first rendered
    shop page; test themes only exist from the Background onwards, so a worker
    that served a shop page earlier has already cached an empty list and nothing
    reloads it.

    The two scenarios catch different failures and both are needed. The first
    one catches a theme carried forward into a channel that has none. The second
    one catches the opposite - a reset that leaves the context believing it has
    already resolved, after which no channel ever gets its theme again.

    Background:
        Given the store operates on a channel named "Poland"
        And the store operates on another channel named "United States"
        And the store has "maverick/meerkat" theme
        And this theme changes homepage template contents to "Onions and bananas"
        And channel "Poland" uses "maverick/meerkat" theme
        And channel "United States" does not use any theme

    @no-api @ui
    Scenario: A channel without a theme does not inherit the one served before it
        When I browse the "Poland" channel
        Then I should see a homepage from that theme
        When I browse the "United States" channel
        Then I should not see a homepage from "maverick/meerkat" theme

    @no-api @ui
    Scenario: A themed channel still gets its theme after a plain one was served
        When I browse the "United States" channel
        Then I should not see a homepage from "maverick/meerkat" theme
        When I browse the "Poland" channel
        Then I should see a homepage from that theme
