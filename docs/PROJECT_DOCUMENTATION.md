RealEstate Platform - Technical Documentation

Summary
- A Laravel-based real estate application with role-based access control (Admin, Agent, User), property management, media uploads, wishlist/compare, messaging, blog, testimonials, schedules, and settings.
- Frontend built with Vite + Tailwind CSS. Authentication via Laravel’s auth scaffolding with Sanctum. Permissions via Spatie.
- Data import/export and PDF/Image processing integrations are present.

Tech Stack
- Backend: PHP (Laravel 9/10 style structure)
- Auth: Laravel Sanctum
- RBAC: spatie/laravel-permission (permission.php, permission tables, PermissionImport/Export)
- Jobs/Queues: Laravel queue (config/queue.php), failed jobs table present
- Database: MySQL/MariaDB (typical for Laravel)
- Mail: Laravel mailer (config/mail.php), ScheduleMail mailable
- Frontend: Vite, Tailwind CSS, PostCSS
- File/Image handling: public/upload directory, Intervention Image (config/image.php)
- PDF/Excel: barryvdh/laravel-dompdf (config/dompdf.php), maatwebsite/excel (config/excel.php)

Repository Layout (key directories)
- app/
  - Console/
  - Exceptions/
  - Exports/ (PermissionExport.php)
  - Http/
    - Controllers/ (AdminController.php, AgentController.php, ProfileController.php, UserController.php and subfolders Agent, Auth, Backend, Frontend)
    - Middleware/ (Authenticate.php, RedirectIfAuthenticated.php, Role.php for RBAC, etc.)
    - Requests/ (Auth, ProfileUpdateRequest.php)
    - Kernel.php (global and route middleware registration)
  - Imports/ (PermissionImport.php)
  - Mail/ (ScheduleMail.php)
  - Models/ (Amenities, Property, MultiImage, Facility, PackagePlan, Wishlist, Compare, PropertyMessage, State, Testimonial, BlogPost/Category, Comment, Schedule, SiteSetting, SmtpSetting, PropertyType, User, ChatMessage)
  - Providers/
  - View/Components/ (AppLayout, GuestLayout)
- bootstrap/
- config/ (app, auth, broadcasting, cache, cors, database, dompdf, excel, filesystems, hashing, image, logging, mail, permission, queue, sanctum, services, session, view)
- database/
  - factories/
  - migrations/ (see Database Schema Overview below)
  - seeders/ (DatabaseSeeder.php, UsersTableSeeder.php)
- public/
  - backend/, frontend/ assets
  - upload/ (admin_images, agent_images, logo, post, property, state, testimonial, user_images)
  - index.php, favicon, robots.txt, .htaccess
- resources/
  - css/, js/ (Vite), views/ (admin, agent, auth, backend, components, errors, frontend, layouts, mail, profile, dashboard, welcome)
- routes/ (web.php, api.php, auth.php, channels.php, console.php)
- storage/
- tests/ (Feature and Unit tests scaffold)
- Tooling: vite.config.js, tailwind.config.js, postcss.config.js, phpunit.xml, composer.json, package.json

Core Features
- Authentication & Users
  - Standard Laravel auth routes (routes/auth.php) and middleware (Authenticate.php, RedirectIfAuthenticated.php)
  - Sanctum is configured for API token/session auth
  - Profile update flows (ProfileController, ProfileUpdateRequest)
- Roles & Permissions
  - Spatie permission setup (config/permission.php, permission tables in migrations)
  - Role middleware enforces access per route/controller
  - Import/Export of permissions via Excel
- Property Management
  - Entities: Property, PropertyType, Amenities, Facilities, MultiImage, State
  - CRUD for properties with multi-image upload and amenities/facilities assignment
  - Property messages (inquiries) and schedules (visit bookings)
- User Engagement
  - Wishlist and Compare lists for properties
  - Testimonials
  - Blog (categories, posts) and comments
  - Chat messaging (ChatMessage) for real-time or near real-time conversation (storage-backed)
- Settings & Administration
  - Site settings, SMTP settings configurable
  - Admin and Agent panels (resources/views/admin, agent)
  - Exports/Imports for permissions; PDF generation available via dompdf
- Media & Assets
  - Public uploads stored in public/upload/* with subfolders per entity type
  - Image processing via Intervention Image (config/image.php)
  - Tailwind + Vite for assets, PostCSS pipeline

Environment Setup
Prerequisites
- PHP 8.1+
- Composer 2+
- Node.js 18+ and npm
- MySQL 8+ (or MariaDB equivalent)

Initial Setup
1) Clone repository and install PHP dependencies
- composer install
2) Copy environment file
- cp .env.example .env (Windows: copy .env.example .env)
3) Configure .env
- APP_NAME, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION, MAIL_FROM_ADDRESS, MAIL_FROM_NAME
- QUEUE_CONNECTION (database/redis/sync) as needed
4) Generate app key
- php artisan key:generate
5) Run migrations and seeders
- php artisan migrate --seed
- Review database/seeders/UsersTableSeeder.php for initial accounts/roles
6) Storage symlink (if uploading via storage)
- php artisan storage:link
7) Install JS dependencies and build assets
- npm install
- npm run dev (for development) or npm run build (for production)
8) Start local server
- php artisan serve

Configuration Notes
- CORS: config/cors.php adjusted as required for API usage
- Queue: config/queue.php dictates driver; update .env and run workers for async mail/exports
- Mail: configure SMTP in .env; ScheduleMail used for booking confirmations/reminders
- Filesystems: config/filesystems.php; uploads currently appear under public/upload/* (directly web-accessible). Consider storage/app/public with storage:link for stricter control

Database Schema Overview (by feature)
- Users & Auth
  - users: standard Laravel users table
  - password_reset_tokens: for password reset
  - personal_access_tokens: for Sanctum API tokens
  - failed_jobs: for failed queued jobs
- RBAC (Spatie)
  - permission tables: roles, permissions, role_has_permissions, model_has_roles, model_has_permissions
- Listings & Metadata
  - property_types: canonical property types
  - amenities: list of amenities (e.g., pool, parking)
  - states: geo administrative units
  - properties: main property entity; relates to type, state, agent/owner; pricing, status, featured flags; slug/SEO fields
  - facilities: property-specific facilities (e.g., schools, hospitals proximity)
  - multi_images: additional images per property
- Engagement
  - wishlists: user-to-property many-to-many
  - compares: user-to-property compare list
  - property_messages: inquiries/messages regarding properties
  - schedules: bookings to visit properties (Schedule and related mail)
  - testimonials: user/admin-managed testimonials
- Content
  - blog_categories, blog_posts, comments: blog and discussion
- Settings
  - smtp_settings: SMTP credentials (alternatively use .env)
  - site_settings: various site-wide settings (name, logo, contact info)
- Communication
  - chat_messages: direct chat messages between users/admin/agents (stores text payloads and sender/receiver references)

Routing & HTTP Layer
- routes/web.php: Web routes for frontend, admin, agent dashboards, and CRUD endpoints
- routes/api.php: API endpoints protected with Sanctum (if exposed)
- routes/auth.php: Login/registration/password routes
- Middleware
  - Authenticate, RedirectIfAuthenticated: standard auth
  - Role: custom or spatie middleware to enforce role-based access (Admin/Agent/User)
  - CSRF: VerifyCsrfToken
  - CORS, TrustProxies/Hosts
- Controllers
  - AdminController, AgentController, UserController, ProfileController
  - Feature-specific controllers within Http/Controllers/Agent, Backend, Frontend namespaces

Views & Frontend
- Blade templates under resources/views with separate admin, agent, frontend folders
- Components in resources/views/components and app/View/Components (AppLayout, GuestLayout)
- Tailwind CSS configured via tailwind.config.js and PostCSS; entrypoints managed with Vite (vite.config.js)
- Images/assets within public/backend and public/frontend assets; dynamic uploads under public/upload/*

Mail & Notifications
- app/Mail/ScheduleMail.php indicates emails for scheduling
- Ensure MAIL_* variables in .env are correct; use a queue for reliable delivery (set QUEUE_CONNECTION=database and run php artisan queue:work)

Imports/Exports & Documents
- app/Exports/PermissionExport.php and app/Imports/PermissionImport.php: uses Maatwebsite Excel for CSV/XLSX handling
- config/dompdf.php: PDF generation capability (e.g., invoices, reports, print-friendly views)
- config/image.php: Intervention Image for resizing/thumbnails

Testing
- PHPUnit configured (phpunit.xml)
- Tests under tests/Feature and tests/Unit
- Commands
  - php artisan test
  - or vendor/bin/phpunit

Common Workflows
- Create an admin user
  - Check database/seeders/UsersTableSeeder.php and DatabaseSeeder for default admin
  - Alternatively, create via tinker: php artisan tinker and assign a role using spatie/permission APIs
- Manage roles/permissions
  - Use spatie APIs or backend UI (if provided in views/backend)
  - Export/import via the Excel integration
- Manage properties
  - Admin/Agent UI for CRUD, image uploads (public/upload/property), amenities/facilities assignment
  - Use Intervention Image for resizing; ensure GD/Imagick is installed
- Handle blog content
  - CRUD for categories and posts, comments moderation
- Schedules and messages
  - Users can send inquiries and book visits; admins/agents receive emails; follow up via chat/messages

Deployment Guide (Production)
- Set APP_ENV=production, APP_DEBUG=false
- Configure optimized autoload and caches
  - composer install --no-dev --optimize-autoloader
  - php artisan config:cache
  - php artisan route:cache
  - php artisan view:cache
- Build assets
  - npm ci
  - npm run build
- Storage permissions and symlink
  - Ensure web server user can write to storage/ and bootstrap/cache/
  - php artisan storage:link (if using storage/app/public)
- Queues and Scheduler
  - Run queue worker: php artisan queue:work --daemon
  - Set up cron for scheduler: * * * * * php /path/to/artisan schedule:run
- Web server
  - Serve public/ as document root
  - Configure HTTPS and security headers

Security Considerations
- Validate and sanitize uploads; restrict mime types and sizes
- Store secrets in .env, not in database where possible
- Enforce authorization via policies/gates and role middleware
- Use CSRF tokens on forms; enable HTTPS and secure cookies
- Rate-limit public endpoints (Laravel Throttle middleware) if APIs are exposed

Troubleshooting
- 500 errors after deploy
  - Clear caches: php artisan optimize:clear
- CSS/JS not updating
  - Rebuild: npm run build; clear view cache; verify Vite manifest paths
- Images not showing
  - Check correct upload path and permissions under public/upload/*
- Mail not sending
  - Verify MAIL_* settings; try smtp.mailtrap.io or a known-good SMTP; check queue worker
- Migration failures
  - Ensure database credentials; drop and re-run migrations in development if needed

Key Commands Reference
- Setup
  - composer install
  - cp .env.example .env
  - php artisan key:generate
  - php artisan migrate --seed
  - php artisan storage:link
  - npm install && npm run dev
- Development
  - php artisan serve
  - npm run dev
  - php artisan tinker
- Maintenance
  - php artisan optimize:clear
  - php artisan queue:work
  - php artisan schedule:run

Notes and Assumptions
- This documentation was authored based on the repository structure and conventional Laravel patterns present in the codebase. Where versions or exact fields were not visible in the file index, standard defaults and best practices are described.
- For a fully authoritative API and route surface, run: php artisan route:list
- For exact model fields, review database/migrations/*.php and app/Models/*.php
