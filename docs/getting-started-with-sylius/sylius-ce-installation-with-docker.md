# Installation

{% hint style="warning" %}
**Traditional Installation**

If you prefer to install Sylius without Docker, use this guide:  <a href="../the-book/sylius-ce-installation/" class="button secondary" data-icon="box-open-full">Sylius Traditional Installation</a>
{% endhint %}

{% stepper %}
{% step %}
## Project Setup

Clone the Sylius-Standard repository, or if you are using GitHub, you can use the 'Use this template' button to create a new repository with Sylius-Standard content.

```bash
git clone git@github.com:Sylius/Sylius-Standard.git your_project_name
```
{% endstep %}

{% step %}
## Development

[Sylius Standard](https://github.com/Sylius/Sylius-Standard) comes with the [docker compose](https://docs.docker.com/compose/) configuration. You can start the development environment via the `make init` command in your favourite terminal. Please note that the speed of building images and initialising containers depends on your local machine and internet connection - it may take some time. Then enter `localhost` in your browser or execute `open http://localhost/` in your terminal.

{% code lineNumbers="true" %}
```bash
make init
open http://localhost/
```
{% endcode %}
{% endstep %}
{% endstepper %}

<details>

<summary>What is Docker</summary>

Docker is an open-source platform for developing, delivering, and running applications. It allows you to separate your application from your infrastructure, simplifying software delivery. With Docker, you can manage infrastructure the same way you manage applications. This platform methodology enables fast code delivery, testing, and implementation, significantly reducing the delay between writing code and running it in the production environment.

{% hint style="warning" %}
Make sure you have [Docker](https://docs.docker.com/get-docker/) and [make](https://www.gnu.org/software/make/manual/make.html/) installed on your local machine.
{% endhint %}

</details>

<details>

<summary>Troubleshooting</summary>

If you encounter errors while running `make init`, such as services failing to start or exiting unexpectedly, it might be caused by **port conflicts** with services already running on your host system.

| Service | Default Host Port | Docker Config Line |
| ------- | ----------------- | ------------------ |
| NGINX   | `80`              | `- "80:80"`        |
| MySQL   | `3306`            | `- "3306:3306"`    |
| Mailhog | `8025`            | `- "8025:8025"`    |

If a process is already using the port, you have two options:

1. **Stop the conflicting service**\
   (e.g. shut down local Apache or MySQL).

{% hint style="success" %}
To check services that are using current ports just run the command:

```bash
lsof -i :80
lsof -i :3306
```
{% endhint %}

2. **Change the default port mappings** in `compose.override.yml`:

```yaml
# examples

nginx:
  ports:
    - "8080:80"

mysql:
  ports:
    - "3307:3306"
```

{% hint style="warning" %}
Remember that if you change your nginx port you will need to correct also the address:

```bash
open http://localhost:8080 # for the example above
```
{% endhint %}

</details>

<details>

<summary>Other Docker dedicated make commands</summary>

Besides the initial `make init` command, the `Makefile` includes several other useful shortcuts for managing your Docker environment:

| Command                     | Description                                                                                             |
| --------------------------- | ------------------------------------------------------------------------------------------------------- |
| `make run`                  | Starts the Docker containers (alias for `make up`).                                                     |
| `make debug`                | Starts containers using `compose.debug.yml`, allowing you to add custom debug tooling or configuration. |
| `make up`                   | Starts all containers in the background using the default Docker configuration.                         |
| `make down`                 | Stops and removes all containers.                                                                       |
| `make clean`                | Stops containers and removes all volumes. This resets your environment.                                 |
| `make install`              | Runs the Sylius installer in non-interactive mode.                                                      |
| `make php-shell`            | Opens a shell in the PHP container. Useful for running CLI commands like `bin/console`.                 |
| `make node-shell`           | Opens a shell in a fresh Node.js container. Good for running JS-related tools like `npm install`.       |
| `make node-watch`           | Runs `npm run watch` inside the Node.js container for automatic frontend asset rebuilding.              |
| `make docker-compose-check` | Verifies that Docker Compose is available and prints the current version.                               |



</details>
