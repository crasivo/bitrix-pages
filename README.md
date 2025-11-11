🔮 Bitrix Pages
===

Ready-made module for 1C-Bitrix that extends the standard page routing functionality.
It is perfect for both developers and portal administrators (boxed version).

Key features:

- 💡 Universal (non-industry specific) solution for any edition.
- 💧 Modern architecture, clean code, and easy scaling.
- 🧩 No third-party modules or PHP extensions required.
- 💫️ New router (MVC) without changing the structure of the public part.
- 🔌 Isolated REST controllers for connecting external systems.
- 🤹‍♂️ Large number of page types (see below).

Possible page types:

- [x] `component` — arbitrary Component;
- [ ] `custom` — reserved type for customization;
- [ ] `file` — reserved for file download _(b_file)_;
- [ ] `json` — reserved for JSON content;
- [x] `html` — arbitrary HTML content;
- [x] `include` — include area _(bitrix:main.include)_;
- [ ] `php` — reserved for PHP content;
- [x] `redirect` — redirect to any other URL.

> All available types are declared in the corresponding enum list [PageContentType](lib/Domain/Api/PageContentType.php).

Possible use cases:

1. Nested pages in existing site sections (crm/tasks/etc).
2. Aliases for old pages after changing component settings (URL).
3. Custom pages with their own static content, dynamic components, etc.
4. Restricting file access for employees.

# 🚀 Installation

Minimum requirements for installing the module:

- MySQL v8.0
- PHP v8.1
- Module `main` v25.700.0

### 1. Download the module

Module location: `/local/modules/crasivo.pages`

Example commands for downloading via console:

```shell
# Via composer
$ composer install crasivo/bitrix-pages
# Via git
$ git clone https://github.com/crasivo/bitrix-pages ./local/modules/crasivo.pages
# Via curl
$ mkdir -p ./local/modules/crasivo.pages
$ curl -sL https://github.com/crasivo/bitrix-pages/archive/refs/heads/main.tar.gz | tar -xzf - --strip-components 1 -C ./local/modules/crasivo.pages
```

### 2. Install and configure the module

The installation process is standard, no additional actions are required.
The installer will check the environment (server/container) and warn you about conflicts.

> Don't forget to check and switch routing from UrlRewrite to the new Routing.

Full list of changes in the file system:

- `/bitrix/.settings.php` — a new file with routes will be added to the `routing` section.
- `/(bitrix|local)/components/crasivo.pages/*` — additional components for display in the public part
- `/(bitrix|local)/routes/pages.php` — file with routes

> The official website has documentation on using [Routing](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&CHAPTER_ID=013764).

# 🕹️ Usage

## Development

### Domain

The subject area is described in the namespace `Crasivo\Pages\Domain`.
It describes all public interfaces, lists, etc.
Behavior modification is available through the standard DI.
To do this, simply declare your class/constructor in the [.settings.php](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2795) file.

Example of overriding an existing service "Creating a component route":

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

The network layer is divided into two parts: `rest` and `web`.
The main part is stored in the namespace `Crasivo\Pages\Network\(Rest|Web)`,
the other is implemented through the `crasivo.pages:*` components.

> [!IMPORTANT]
> Access to all controllers is limited by filters of type `Authentication` & `ModuleRight`.
> In other words, the user must be authorized and have minimal access rights to the module itself (see settings).

### Infrastructure

The infrastructure layer associated with Bitrix is stored in a separate reserved namespace `Crasivo\Pages\Integration`.

> [!NOTE]
> Other NON-vendor integrations are supposed to be stored in the `Infrastructure` space.

> [!WARNING]
> Never use or access objects from this layer without DI!
> All classes are marked with the `@internal` attribute, i.e. for internal use.
> The structure, nesting, and names may change at any time (release).

### UseCase

> This layer and section are under development.

# Maintenance and support

### 📌 TODO

List of planned tasks (wishes):

1. Add a router dump to a separate cache file so as not to access the registry again.
2. Add other types of requests (POST/DELETE/PUT/etc).
3. Organize a full User-Role-Permission binding at the level of groups, pages, departments (Bitrix24).

---

## 📜 License

This project is distributed under the [MIT](https://en.wikipedia.org/wiki/MIT_License) license.
The full text of the license can be read in the [LICENSE](LICENSE) file.
