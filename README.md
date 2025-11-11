🔮 Bitrix Pages
===

A ready-made module for 1C-Bitrix that extends the built-in page routing functionality.
It's perfect for both developers and portal administrators (out-of-the-box).

Key features:

- 💡 Universal (non-industry-specific) solution for any edition.
- 💧 Modern architecture, clean code, and easy scaling.
- 🧩 No third-party modules or PHP extensions required.
- 💫️ New router (MVC) without changing the public domain structure.
- 🔌 Isolated REST controllers for connecting external systems.
- 🤹‍♂️ Large number of page types (see below).

Possible page types:

- [x] `component` — any component;
- [ ] `custom` — reserved type for customization;
- [ ] `file` — reserved for file downloads (_(b_file)_);
- [ ] `json` — reserved for JSON content;
- [x] `html` — arbitrary HTML content;
- [x] `include` — included area (_(bitrix:main.include)_);
- [ ] `php` — reserved for PHP content;
- [x] `redirect` — redirect to any other URL.

> All available types are declared in the corresponding enum [PageContentType](lib/Domain/Api/PageContentType.php).

Possible use cases:

1. Nested pages in existing site sections (crm/tasks/etc).
1. Aliases for old pages after changing component settings (URL).
1. Custom pages with their own static content, dynamic components, etc.
1. Restricting file access for employees.

# 🚀 Installation

Minimum requirements for module installation:

- MySQL v8.0
- PHP v8.1
- `main` module v25.700.0

### 1. Load the module

Module location: `/local/modules/crasivo.pages`

Example commands for loading via console:

```shell
# Via composer
$ composer install crasivo/bitrix24-pages
# Via git
$ git clone https://github.com/crasivo/bitrix24-pages ./local/modules/crasivo.pages
# Via curl
$ curl https://github.com/crasivo/n8n-standalone/archive/refs/heads/main.tar.gz | tar -xzf - --strip-components 1 ./local/modules/crasivo.pages
# Via wget
$ wget https://github.com/crasivo/n8n-standalone/archive/refs/heads/main.tar.gz | tar -xzf - --strip-components 1 ./local/modules/crasivo.pages
```

### 2. Install and configure the module

The installation process is standard; no additional actions are required.
The installer will automatically check the environment (server/container) and warn you of any conflicts.

> Don't forget to check and switch routing from UrlRewrite to the new Routing.

Full list of changes in the file system:

- `/bitrix/.settings.php` — a new file with routes will be added to the `routing` section.
- `/(bitrix|local)/components/crasivo.pages/*` — additional components for display in the public domain
- `/(bitrix|local)/routes/pages.php` — file with routes

> The official website has documentation on using [Routing](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=013764).

# Operation

## Development

### Domain

The subject area is described in the `Crasivo\Pages\Domain` namespace.
All public interfaces, lists, etc. are described there.
Behavior modification is available through the standard DI. To do this, simply declare your class/constructor in the [.settings.php](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2795) file.

Example of overriding the existing "Creating a Route Component" service:

```php
return [
'services' => [
'value' => [
Crasivo\Pages\Domain\Api\CreatePageComponent::class => [
'constructor' => fn() => new Your\Custom\Service(),
],
],
],
];
```

### Network

The network layer is divided into two parts: `rest` and `web`. The main part is stored in the Crasivo\Pages\Network\(Rest|Web) namespace,
the other part is implemented through the crasivo.pages:* components.

> [!IMPORTANT]
> Access to all controllers is restricted by filters like Authentication and ModuleRight.
> In other words, the user must be authorized and have minimal access rights to the module itself (see settings).

### Infrastructure

The infrastructure layer associated with Bitrix is stored in a separate reserved namespace: Crasivo\Pages\Integration.

> [!NOTE]
> Other non-vendor integrations are expected to be stored in the Infrastructure namespace.

> [!WARNING]
> Never use or access objects from this layer without DI!
> All classes are marked with the @internal attribute, meaning they are for internal use only.
> The structure, nesting, and names may change at any time (release).

# Maintenance and Support

### 📌 TODO

1. Add a router dump to a separate cache file to avoid unnecessary registry access.
1. Add other request types (POST/DELETE/PUT/etc.).
1. Organize a full-fledged User-Role-Permission relationship at the group, page, and department levels (Bitrix24).

---

## 📜 License

This project is distributed under the [MIT] license (https://en.wikipedia.org/wiki/MIT_License).
The full license text can be found in the [LICENSE] file.
