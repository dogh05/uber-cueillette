# Uber-Cueillette — Agricultural Employment Platform

An individual academic web project connecting Tunisian farmers looking for harvest workers with workers seeking agricultural employment.

Built by **Ahmed Doghman**, a Business Information Systems student at ISG Tunis, to apply web development and relational database concepts to an agricultural recruitment workflow.

## Main features

- Registration and login for farmers and workers.
- Separate dashboards for each user type.
- Harvest job offers with dates, location, fruit type, workforce needs, and daily pay.
- Job applications and farmer-side application management.
- Worker application history, evaluations, and profile management.
- Dashboard statistics and charts.
- Client-side and server-side form validation.

## Technology stack

| Area | Technologies |
| --- | --- |
| Frontend | HTML, CSS, Bootstrap 5, JavaScript |
| Dynamic interactions | Fetch/AJAX and JSON |
| Backend | PHP, PDO, sessions |
| Database | MySQL / MariaDB |
| Charts and interface assets | Chart.js, Font Awesome, Google Fonts |

The interface is in French. Several assets load from external CDNs, so an internet connection is needed for the complete visual experience.

## My contribution

I developed the frontend, PHP endpoints, relational database, and the farmer and worker workflows as an individual academic project.

## Run locally

### Requirements

- PHP 8.x with PDO MySQL support.
- MySQL or MariaDB.
- A local PHP environment such as XAMPP or WAMP, or the PHP development server.

### 1. Prepare the database

Create an empty database named `uber_cueillette` with the `utf8mb4` character set, then import `uber_cueillette.sql` using phpMyAdmin or a MySQL client.

```bash
mysql -u YOUR_DATABASE_USER -p uber_cueillette < uber_cueillette.sql
```

The SQL file includes the table structure and reference lists for governorates and fruit types. Original account records, passwords, and profile photos have been removed. Create fictional accounts through the registration screen to try the workflows.

### 2. Configure the connection

Edit the database constants in `php/config.php` to match your local installation:

- `DB_HOST`: database host.
- `DB_NAME`: `uber_cueillette`.
- `DB_USER`: your local database user.
- `DB_PASS`: your local database password.

Do not commit personal database credentials after editing this file.

### 3. Start the application

From the project directory:

```bash
php -S localhost:8000
```

Open http://localhost:8000/index.html in your browser. Alternatively, put the project in your local Apache document root and open it through localhost. Opening the HTML directly from the filesystem will not run the PHP endpoints.

### 4. Try the workflow

1. Register a fictional farmer account and create a job offer.
2. Log out and register a fictional worker account.
3. Apply for the offer as the worker.
4. Log in as the farmer to review applications.

The current registration form requires an eight-digit CIN-format value, a letters-only username, and a password of at least eight characters ending in `$` or `#` (maximum 72 bytes). Worker registration also requires a description and an image. Use dummy data and a non-personal image.

## Updating an existing local installation

Back up your database before changing its schema. For an existing installation, run `migrations/001_password_hash_columns.sql` once to widen the password columns. Do not re-import the full SQL export over an existing database.

Existing plain-text passwords are deliberately not accepted by the updated login. For a disposable demo database, create fresh fictional accounts with this version. Existing accounts that must be retained need a controlled password reset or migration before they can log in; widening the columns alone does not convert their passwords.

## Repository structure

| Path | Purpose |
| --- | --- |
| `index.html` | Landing page, registration, and login |
| `dashboard_agriculteur.html` | Farmer dashboard |
| `dashboard_ouvrier.html` | Worker dashboard |
| `php/` | Database configuration and backend endpoints |
| `validation.js` | Frontend validation helpers |
| `style.css` | Shared styling |
| `uber_cueillette.sql` | Database schema and non-personal reference data |

## Current scope and improvements

This is an academic prototype intended for local demonstration. New passwords are stored as hashes using `password_hash()` and checked with `password_verify()`. Password columns support 255 characters, and the session ID is renewed after login. CSRF protection, output escaping, upload checks, and error handling still need a dedicated review before a public deployment.

Only static inspection was performed when preparing this repository package. PHP execution, SQL import, and end-to-end workflows have not been verified in that preparation environment.

## Author

Ahmed Doghman — [GitHub](https://github.com/dogh05) · [LinkedIn](https://www.linkedin.com/in/ahmed-doghman-377034415/)
