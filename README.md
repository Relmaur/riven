# Riven

**Riven** is a lightweight, custom PHP framework built from the ground up. It follows modern development principles like OOP, MVC, and PSR-4 autoloading, designed to be the robust "iron" skeleton for your next web application.

Unlike a traditional CMS, Riven provides the essential building blocks—Routing, DI, Migrations, and Auth—allowing you to forge anything from simple blogs to complex SaaS platforms without the bloat.

## Core Features

-   **Declarative Routing:** A clean, expressive routing system defined in `routes/` for both Web and API.
-   **Dependency Injection:** A powerful Service Container that manages class dependencies and app wiring automatically.
-   **Database Migrations:** A professional, reversible migration system to version-control your database schema.
-   **Event-Driven Architecture:** Decoupled logic using a robust Events and Listeners system.
-   **Built-in Authentication:** A secure, session-based user registration and login system out of the box.
-   **Files & Mailing:** Integrated support for file uploads and SMTP email handling via Symfony Mailer.
-   **Developer CLI:** A suite of console tools (`bin/`) to generate code, manage migrations, and back up data.
-   **Test-Ready:** Comes with a full PHPUnit suite for both Unit and Feature testing.

## Prerequisites

-   PHP 8.1 or higher
-   Composer ([https://getcomposer.org/](https://getcomposer.org/))
-   A local development server (e.g., Laravel Herd, XAMPP, or PHP built-in server)
-   SQLite PHP extension enabled

## Installation and Setup

1.  **Clone the repository:**

    ```bash
    git clone [https://github.com/Relmaur/riven](https://github.com/Relmaur/riven) riven
    cd riven
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    ```

3.  **Environment Setup:**
    Create a `.env` file in the project root by copying the example file.

    ```bash
    cp .env.example .env
    ```

    *Tip: Open `.env` and configure your `MAILER_DSN` if you plan to test email features.*

4.  **Initialize the Database:**
    Riven uses SQLite by default. Run the bootstrap script to create the database file and migration table:

    ```bash
    php database/migrations_setup.php
    ```

5.  **Run Migrations:**
    Create the necessary tables (users, posts, etc.):

    ```bash
    composer migrate
    ```

6.  **Serve the Application:**
    Point your local server's web root to the `/public` directory, or use the PHP built-in server:

    ```bash
    php -S localhost:8000 -t public
    ```

## CLI Commands

Riven comes with a "Forge" of tools to speed up development.

### Database & Migrations
-   `composer migrate`: Runs all pending migrations.
-   `composer migrate:rollback`: Reverts the last batch of migrations.
-   `composer make-migration <Name>`: Creates a new timestamped migration file.
    -   *Example:* `composer make-migration CreateOrdersTable`
-   `composer db:backup`: Creates a timestamped snapshot of your SQLite database in `/database/backups`.

### Code Generation
-   `composer make:controller <Name>`: Generates a new Controller class.
    -   *Example:* `composer make:controller Api/ProductsController`
-   `composer make:model <Name>`: Generates a new Model class.
    -   *Example:* `composer make:model Product`

## Testing

Riven is built with testing in mind. The suite includes an in-memory SQLite database configuration for fast, isolated feature tests.

```bash
composer test