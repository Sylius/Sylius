---
description: Shopping via AI in Sylius (Experimental)
---

# 🤖 AI Conversational Commerce

{% hint style="warning" %}
This feature is currently in beta, leveraging the Model Context Protocol (MCP). \
Use it cautiously in production environments and expect potential changes.
{% endhint %}

## Introduction

Sylius integrates artificial intelligence capabilities via the experimental Model Context Protocol (MCP). This enables customers to interact with your Sylius shop using natural language, enhancing user engagement by providing personalized shopping assistance, recommendations, and allowing customers to place orders directly through conversational AI.

## What is MCP?

The Model Context Protocol (MCP) is a standardized protocol to connect language models (such as ChatGPT) with external tools, APIs, and systems. MCP allows AI models to make structured calls (similar to function calls) during conversational interactions.

The MCP Server acts as a bridge between the language model and your Sylius application logic. It exposes specific tools (like product searches or order creation) and executes them based on requests from the AI.

Sylius integrates with MCP through the official [`php-mcp/server`](https://github.com/php-mcp/server) package, enabling AI agents to interact seamlessly with your shop (search products, check prices, initiate checkout, and more).

```
┌────────────────┐                  ┌───────────────┐       ┌────────┐
│  MCP Client    │◄────────────────►│  MCP Server   │◄─────►│ Sylius │
│ (OpenAi, etc.) │ (Stdio/HTTP/SSE) │ (Tools, etc.) │ (API) │        │
└────────────────┘                  └───────────────┘       └────────┘
```

To learn more, see the [official MCP introduction](https://modelcontextprotocol.io/introduction).

## How It Works

The Sylius MCP implementation provides a standardized interface for conversational commerce. This integration allows your customers to:

* Receive personalized product recommendations based on user preferences
* Ask and receive answers to product-related queries
* Quickly find products using conversational AI
* Directly place orders through conversational interactions, streamlining the purchasing process

## Installation

### **Requirements**

| Package       | Version |
| ------------- | ------- |
| PHP           | ^8.2    |
| sylius/sylius | ^2.1    |
| MySQL         | ^8.4    |
| NodeJS        | ^20.x   |

{% hint style="info" %}
This installation assumes you're using Symfony Flex.
{% endhint %}

1. Require the plugin:

```bash
composer require sylius/mcp-server-plugin
```

2. Clear the application cache:

```bash
bin/console cache:clear
```

### Running the MCP Server

Start the server with:

```bash
bin/console sylius:mcp-server:start
```

The default address is: [http://localhost:8080/mcp](http://localhost:8080/mcp). It uses Streamable HTTP Transport by default.

## MCP Server Configuration

Example default configuration:

```yaml
sylius_mcp_server:
  server:
    name: 'Sylius MCP Server'
    version: '0.1.0'
    transport:
      host: 127.0.0.1
      port: 8080
      prefix: 'mcp'
      enable_json_response: false
      ssl:
        enabled: false
        context: []

    session:
      driver: cache
      ttl: 3600

    discovery:
      locations:
        - { base_path: '%sylius_mcp_server.plugin_root%', scan_dirs: ['src/Tool'] }
```

To enable SSL, set `ssl.enabled` to `true` and configure the context as described in the [php-mcp/server documentation](https://github.com/php-mcp/server?tab=readme-ov-file#ssl-context-configuration).

## Transport Customization

To implement a custom transport, create a factory implementing `Sylius\McpServerPlugin\Factory\ServerTransportFactoryInterface`, and override the transport service in the `McpServerCommand`.

## Sylius API Integration

The tools interact with Sylius through HTTP clients defined as:

```yaml
sylius_mcp_server.http_client.api_shop:
    base_uri: '%sylius_mcp_server.api.shop_base_uri%'
    headers:
        - 'Accept: application/ld+json'
        - 'Content-Type: application/ld+json'
sylius_mcp_server.http_client.api_shop_merge_patch:
    base_uri: '%sylius_mcp_server.api.shop_base_uri%'
    headers:
        - 'Accept: application/ld+json'
        - 'Content-Type: application/merge-patch+json'
```

The default base URI is `http://localhost:8000/api/v2/shop/`, but you can override it using:

```env
SYLIUS_MCP_SERVER_API_SHOP_BASE_URI=http://your-custom-url
```

## Available Tools

The tools listed below represent actions that AI agents can invoke during a conversational shopping session. Each tool corresponds to a step in the customer journey, from browsing to placing an order. These tools are implemented as callable endpoints exposed by the MCP server.

For example:

* Browsing and search are handled via `search_products` and `search_product_variants`
* Checkout is orchestrated through steps like `create_order`, `update_order_address`, `select_shipping_method`, `select_payment_method`, and `complete_checkout`
* Order context is maintained with tools like `add_item_to_order`, `fetch_order`, or `fetch_channel`

| Name                      | Description                              |
| ------------------------- | ---------------------------------------- |
| add\_item\_to\_order      | Adds an item to the order.               |
| complete\_checkout        | Completes the checkout process.          |
| create\_order             | Creates a new order.                     |
| fetch\_channel            | Fetches a channel by code.               |
| fetch\_currency           | Fetches a currency by code.              |
| fetch\_order              | Fetches an order by its token.           |
| fetch\_product            | Fetches a product by its code.           |
| fetch\_product\_variant   | Fetches a product variant by its code.   |
| list\_payment\_methods    | Lists all available payment methods.     |
| list\_shipping\_methods   | Lists all available shipping methods.    |
| search\_products          | Searches for products by name.           |
| search\_product\_variants | Searches for product variants by name.   |
| select\_payment\_method   | Selects a payment method for the order.  |
| select\_shipping\_method  | Selects a shipping method for the order. |
| update\_order\_address    | Updates the order address.               |

{% hint style="warning" %}
These tools currently operate in guest mode only — `fetch_order` returns data only if the order has no associated customer account.
{% endhint %}

### Adding Custom Tools

To define your own tools:

1. Create a PHP class annotated with `#[McpTool]`
2. Ensure the directory is scanned:

```yaml
sylius_mcp_server:
  server:
    discovery:
      locations:
        - { base_path: 'your_base_path', scan_dirs: ['your/custom/Tool/Directory'] }
```

Refer to the [php-mcp/server documentation](https://github.com/php-mcp/server?tab=readme-ov-file#-defining-mcp-elements) for structuring custom tools.

## Usage Example

Use the server in the [OpenAI Playground](https://platform.openai.com/playground/prompts) via [ngrok](https://ngrok.com/), or similar tunneling tools.

**OpenAI Playground Setup**

1.  Add a tool:\
    \


    <figure><img src="../.gitbook/assets/image (70).png" alt=""><figcaption></figcaption></figure>
2. Configure it:
   * **URL**: `http://localhost:8080/mcp` or your ngrok URL
   * **Label**: Sylius
   *   **Authentication**: None\
       \


       <figure><img src="../.gitbook/assets/image (71).png" alt=""><figcaption></figcaption></figure>

### **API Call Example**

```bash
curl https://api.openai.com/v1/responses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $OPENAI_API_KEY" \
  -d '{
    "model": "gpt-4.1",
    "tools": [
      {
        "type": "mcp",
        "server_label": "Sylius",
        "server_url": "$YOUR_MCP_SERVER_URL",
        "require_approval": "never"
      }
    ],
    "input": [
      {
        "role": "user",
        "content": [
          {
            "type": "input_text",
            "text": "Find me red shoes"
          }
        ]
      }
    ],
    "max_output_tokens": 1024
  }'
```

#### Testing and Debugging

To test your MCP integration, use tools like Postman or cURL to validate responses. Logs are available at:

```bash
var/log/prod.log
# or
var/log/dev.log
```

## Next Steps and Further Development

As the Sylius MCP integration is in beta, we welcome feedback and contributions. Monitor the official plugin repository and the [Sylius blog](https://sylius.com/blog/) for updates.

## Read more

* 🔗 [Sylius MCP Server Plugin repository](https://github.com/Sylius/McpServerPlugin)
