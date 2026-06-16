# AskDocPH

## Overview
AskDocPH is a comprehensive, full-stack mental health and teleconsultation web application built for the Philippines. It serves to connect patients with verified mental health professionals (doctors/psychologists), providing a safe platform for professional consultations, community support, crisis management, and personal mental health tracking. The system is designed to provide accessible mental healthcare while ensuring the verification and credibility of its medical professionals.

## Live Demo
Not yet deployed

## Screenshots
![Main Page](./screenshots/main.png)
![Dashboard](./screenshots/dashboard.png)
*(Add your screenshots to a /screenshots folder in the root)*

## Features

### Patient Features
* **Dashboard & Mood Tracking**: Daily mood logging with history charts spanning the last 14 days.
* **Doctor Discovery & Booking**: Search and filter approved doctors by specialization or availability, and book appointments based on available schedule slots.
* **AI-Assisted Chat**: A built-in AI chatbot capable of providing initial mental health recommendations or directing users to find a doctor/crisis support based on keywords (e.g., suicide, self-harm, depression).
* **Crisis Reporting**: Immediate reporting of critical mental health states, which alerts administrators.
* **Social Feed & Community**: A community feed where patients can post anonymously or publicly, share text/media, like, comment, and participate in community polls.
* **Resources & Bookmarking**: Access to health resources (articles, media) with the ability to organize bookmarks into collections.
* **Help Requests**: Direct requests for help sent to specific verified doctors.
* **Identity Verification**: ID upload process to ensure the authenticity of patient accounts.

### Doctor Features
* **Application & Verification**: A rigorous onboarding process requiring the submission of professional titles, PRC licenses, hospital affiliations, and documents for admin review.
* **Schedule Management**: Define available weekly schedules, block out specific dates, and manage appointment requests (confirm, cancel, complete).
* **Patient Consultations**: View patient mood history before appointments, add private consultation notes, and receive post-appointment reviews.
* **Referral System**: Refer patients to other verified doctors within the platform, complete with reasons and private messages.
* **Resource Sharing**: Create and publish health resources and articles to the platform.
* **Direct Messaging**: Engage in one-on-one secure messaging with patients (after a help request is accepted) or other doctors.

### Admin Features
* **Comprehensive Dashboard**: Real-time statistics and charts on total patients, doctors, pending applications, and crisis reports.
* **Verification Management**: Review, approve, or reject doctor applications and patient ID verifications.
* **Crisis Management**: Respond to and resolve escalated user crisis reports.
* **System Reports & Analytics**: Export detailed reports (CSV/PDF) for users, appointments, and crisis incidents.
* **Content Moderation**: Manage announcements, daily affirmations, and oversee the platform's social feed and audit logs.
* **User Management**: View and manage all registered users on the platform.

### Shared / System Features
* **Role-Based Access Control**: Distinct layouts, middleware, and permissions for Admin, Doctor, and Patient roles.
* **Real-time Messaging**: Direct messaging system with read receipts and unread message counters.
* **Post Analytics**: Engagement tracking (likes, comments, shares) and reaction breakdowns for posts.
* **Dark Mode**: Toggleable dark mode preference saved per user.
* **Two-Factor Authentication (2FA)**: Additional security layer available for user accounts.
* **Notification System**: In-app notifications for appointments, referrals, help requests, and community interactions.

## Tech Stack

| Category       | Technology |
|----------------|------------|
| Frontend       | Blade Templates, Alpine.js, Tailwind CSS (v3/v4 via Vite) |
| Backend        | PHP 8.2+, Laravel 12.0 |
| Database       | SQLite (Configurable via .env) |
| Authentication | Laravel Breeze (with custom 2FA & ID verification) |
| Styling        | Tailwind CSS, PostCSS |
| Deployment     | Local / Traditional Hosting (No specific Vercel config found) |
| Build Tool     | Vite, npm |

## Project Structure

```text
c:\laragon\www\askdocph/
├── app/                  # Application core logic
│   ├── Console/          # Artisan console commands
│   ├── Http/             # Controllers, Middleware, and Requests
│   │   ├── Controllers/  # Route controllers grouped by Admin, Doctor, Patient, and Shared
│   │   └── Middleware/   # Custom middlewares (Role, ID Verification, Email Verification)
│   ├── Models/           # Eloquent Models defining the database relationships
│   ├── Notifications/    # Application notification classes
│   ├── Providers/        # Service providers
│   └── Services/         # Reusable business logic (e.g., AuditService)
├── bootstrap/            # Framework bootstrapper
├── config/               # Application configuration files
├── database/             # Database files
│   ├── factories/        # Model factories for testing
│   ├── migrations/       # Database schema migrations defining 50+ tables
│   ├── seeders/          # Database seeders (Admin, Test Patient, Test Doctor)
│   └── database.sqlite   # SQLite database file
├── public/               # Publicly accessible assets (compiled CSS/JS, uploads)
├── resources/            # Uncompiled assets and views
│   ├── css/              # Tailwind CSS entry points
│   ├── js/               # Alpine.js and custom scripts
│   └── views/            # Blade templates grouped by role (admin, doctor, patient, shared)
├── routes/               # Application routing definitions
│   ├── auth.php          # Authentication routes (Breeze)
│   ├── console.php       # Console routes
│   └── web.php           # Main web application routes mapped to controllers
├── storage/              # Uploaded files, application logs, and framework caches
├── tests/                # Automated PHPUnit tests
├── vendor/               # Composer dependencies
└── node_modules/         # NPM dependencies
```

## Database Schema

The application relies on a highly relational database schema. Here are the primary models and their key relationships:

* **User**: The core authentication model.
  * *Columns*: `role` (admin, doctor, patient), `email`, `username`, `password`, `doctor_status`, `id_verification_status`, profile fields.
  * *Relations*: HasMany Posts, Appointments, DoctorSchedules, HelpRequests, Messages, Groups, etc.
* **Appointment**: Manages consultation bookings.
  * *Columns*: `patient_id`, `doctor_id`, `schedule_id`, `appointment_date`, `start_time`, `end_time`, `status`, `meeting_link`.
  * *Relations*: BelongsTo Patient (User), Doctor (User), Schedule; HasMany AppointmentNotes.
* **DoctorSchedule**: Defines available times for doctors.
  * *Columns*: `doctor_id`, `day_of_week`, `start_time`, `end_time`, `is_available`.
* **DoctorApplication**: Handles the onboarding verification for doctors.
  * *Columns*: `user_id`, `status`, `reviewed_by_admin_id`, `admin_notes`.
  * *Relations*: BelongsTo User; HasMany Documents, ProfessionalTitles.
* **Post & Community**: Drives the social feed.
  * *Models*: `Post`, `PostLike`, `PostComment`, `PostMedia`, `CommunityPoll`.
  * *Relations*: Posts belong to Users and Groups. Posts have many Likes, Comments, Media.
* **Conversation & Message**: Powers the direct messaging system.
  * *Models*: `Conversation`, `ConversationParticipant`, `Message`.
  * *Relations*: Conversations have many Participants and Messages.
* **CrisisReport**: Escalates critical patient states to admins.
  * *Columns*: `user_id`, `description`, `status`, `responded_by`.
* **DoctorReferral**: Enables doctors to refer patients to colleagues.
  * *Columns*: `referring_doctor_id`, `referred_to_doctor_id`, `patient_id`, `appointment_id`, `reason`, `status`.

## Local Setup Instructions

### Prerequisites
* PHP >= 8.2
* Composer
* Node.js & npm
* SQLite (default) or MySQL/PostgreSQL

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd askdocph
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install
   ```

4. **Set up Environment Variables:**
   Duplicate the example environment file:
   ```bash
   cp .env.example .env
   ```
   *Note: Ensure `DB_CONNECTION=sqlite` is set in your `.env` for the easiest local setup.*

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Create the SQLite Database (if using SQLite):**
   ```bash
   touch database/database.sqlite
   ```

7. **Run Database Migrations and Seeders:**
   This will create the schema and seed the default admin, a test patient, and a test doctor account.
   ```bash
   php artisan migrate --seed
   ```

8. **Link Storage (for media/profile uploads):**
   ```bash
   php artisan storage:link
   ```

9. **Run the Application:**
   You will need two terminal windows to run both the Laravel backend and Vite frontend development servers simultaneously:
   
   *Terminal 1:*
   ```bash
   php artisan serve
   ```
   
   *Terminal 2:*
   ```bash
   npm run dev
   ```

   Alternatively, if you are using Laravel Sail or a tool like Concurrently, the project includes a dev script:
   ```bash
   npm run dev
   ```
   *(This triggers a concurrently command running the PHP server, queue listener, logs, and Vite).*

### Environment Variables

If you need to configure the `.env` file manually, create a `.env` in the root and configure these essential variables:

```env
APP_NAME=AskDocPH
APP_ENV=local
APP_KEY= # Automatically populated by php artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# If using MySQL instead of SQLite:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=askdocph
# DB_USERNAME=root
# DB_PASSWORD=

MAIL_MAILER=log # Use log for local development to see emails in storage/logs/laravel.log

# Configure for file uploads (defaults to local/public)
FILESYSTEM_DISK=local
```

## Deployment

No specific automated deployment configuration (like `vercel.json` or Forge recipes) was found in the repository. Standard Laravel deployment practices apply:

1. Provision a server with PHP 8.2+, a web server (Nginx/Apache), and a database.
2. Clone the repository and run `composer install --optimize-autoloader --no-dev`.
3. Set the `.env` variables appropriate for production (`APP_ENV=production`, `APP_DEBUG=false`, configure `DB_*`).
4. Run migrations: `php artisan migrate --force`.
5. Compile frontend assets: `npm install && npm run build`.
6. Ensure the web server points to the `/public` directory and `storage/` & `bootstrap/cache/` are writable.
7. Run `php artisan storage:link`.

## Author
Dexter Balbuena
3rd Year IT Student — FSUU University
Open to freelance: Full-Stack · UI/UX · AI Projects

## License
MIT License
