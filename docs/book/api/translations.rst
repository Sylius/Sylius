Translations
============

In the shop part of our API translatable entities are translated at the server-side and users get an already translated field in the response.
By default it will be provided in default locale configured on the channel, different locale must be configured in the header (e.g. `Accept-Language: de-DE`)

.. warning::

    Remember that the chosen locale has to be enabled in the channel!

.. note::

    Though Sylius' locale code format (e.g. `de_DE`) will also be accepted in `Accept-Language` header, it's not valid value according to HTTP specification.
    Also, it silently changes CORS behavior - results with CORS restrictions because the "_" character is not considered safe for this header type (https://developer.mozilla.org/en-US/docs/Glossary/CORS-safelisted_request_header).
