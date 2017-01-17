Your First Email
================

Let's say you want to send a notification to the website team when someone submits a new position to your movie catalog website!

You can do it in few simple steps:

Configure Your E-Mail
---------------------

In your ``app/config/config.yml``, under ``sylius_mailer`` you should configure the email:

.. code-block:: yaml

    # app/config/config.yml

    sylius_mailer:
        sender:
            name: Movie Database Example
            address: no-reply@movie-database-example.com
        emails:
            movie_added_notification:
                subject: A new movie {{ movie.title }} has been submitted
                template: AppBundle:Email:movieAddedNotification.html.twig

That's it! Your unique code is "movie_added_notification". Now, let's create the template.

Creating Your Template
----------------------

In your ``app/Resources/views/Email:movieAddedNotification.html.twig`` put the following Twig code:

.. code-block:: twig

    {% block subject %}
        A new movie {{ movie.title }} has been submitted
    {% endblock %}

    {% block body %}
        Hello Movie Database Example!

        A new movie has been submitted for review to your database.

        Title: {{ movie.title }}
        Added by {{ user.name }}

        Please review it and accept or reject!
    {% endblock %}

That should be enough!

Sending The E-Mail
------------------

The service responsible for sending an e-mail has id ``sylius.email_sender``. All you need to do is retrieve it from the container or inject to a listener:

.. code-block:: php

    <?php

    namespace App\AppBundle\Controller;

    use Symfony\Component\HttpFoundation\Request;

    class MovieController
    {
        public function submitAction(Request $request)
        {
            // Your code.

            $this->get('sylius.email_sender')->send('movie_added_notification', array('team@website.com'), array('movie' => $movie, 'user' => $this->getUser()));
        }
    }

Listener example:

.. code-block:: php

    <?php

    namespace App\AppBundle\Controller;

    use App\Event\MovieCreatedEvent;
    use Sylius\Component\Mailer\Sender\SenderInterface;

    class MovieNotificationListener
    {
        private $sender;

        public function __construct(SenderInterface $sender)
        {
            $this->sender = $sender;
        }

        public function onMovieCreation(MovieCreatedEvent $event)
        {
            $movie = $event->getMovie();
            $user = $event->getUser();

            $this->sender->send('movie_added_notification', array('team@website.com'), array('movie' => $movie, 'user' => $user));
        }
    }

We recommend using events approach, but you can send e-mails from anywhere in your application. Enjoy!
