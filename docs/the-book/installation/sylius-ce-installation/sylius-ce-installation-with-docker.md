---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Sylius CE Installation with Docker

### Docker

Docker is an open-source platform for developing, delivering, and running applications. It allows you to separate your application from your infrastructure, simplifying software delivery. With Docker, you can manage infrastructure the same way you manage applications. This platform methodology enables fast code delivery, testing, and implementation, significantly reducing the delay between writing code and running it in the production environment.

{% hint style="info" %}
Make sure you have [Docker](https://docs.docker.com/get-docker/) and [make](https://www.gnu.org/software/make/manual/make.html/) installed on your local machine.
{% endhint %}

### Project Setup

Clone Sylius-Standard repository or if you are using GitHub you can use the _Use this template_ button that will create new repository with Sylius-Standard content.

```bash
git clone git@github.com:Sylius/Sylius-Standard.git your_project_name
```

### Development

[Sylius Standard](https://github.com/Sylius/Sylius-Standard) comes with the [docker compose](https://docs.docker.com/compose/) configuration. You can start the development environment via the `make init` command in your favourite terminal. Please note that the speed of building images and initialising containers depends on your local machine and internet connection - it may take some time. Then enter `localhost` in your browser or execute `open http://localhost/` in your terminal.

{% code lineNumbers="true" %}
```bash
make init
open http://localhost/
```
{% endcode %}
