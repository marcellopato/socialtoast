# SocialToast - Document Management System (DMS) Design

## 1. Development Process & AI Leverage
- **Methodology**: Agile/Iterative approach properly tracking tasks (task.md) and progress.
- **AI Tools**: Used extensively for:
    - **Code Generation**: Boilerplate for Controllers, Livewire components, and Tests.
    - **Debugging**: Analyzing error logs (e.g., Google Drive 401 errors) and suggesting fixes.
    - **Documentation**: Generating README and Architecture docs.
    - **Persona Design**: Creating the "Auditor Persona" prompt for the Gemini AI.

## 2. Cloud Provider & Platform
- **Provider Choice**: **Google Cloud Platform (GCP)** (Simulated/Proposed).
    - *Reason*: Native integration with Gemini AI and Google Drive (as requested/implemented). GCP offers robust serverless options (Cloud Run) perfect for this app.
- **Compute Platform**: **Containerized (Docker) on Serverless (e.g., Cloud Run)**.
    - *Scalability*: Serverless allows auto-scaling from zero to manageable traffic spikes (e.g., end-of-month uploads) without over-provisioning.
    - *Performance*: Stateless containers ensure consistency. Queue workers can scale independently for document processing.

## 3. CI/CD & Infrastructure as Code (IaC)
- **CI/CD Pipeline** (GitHub Actions):
    - **CI**: Runs on every Push/PR. Executes PHPUnit tests, Static Analysis (Larastan/Pint), and Frontend Build (Vite).
    - **CD**: Automates deployment to staging/production when merging to `main`.
- **IaC**:
    - **Terraform** or **Laravel Forge/Envoyer** (Suggested) for managing infrastructure provision (Database, Redis, Storage Buckets).

## 4. Observability & Metrics
- **Strategy**:
    - **Laravel Pulse**: (Implemented) For real-time "at-a-glance" monitoring of slow queries, jobs, and server status.
    - **Application Monitoring (APM)**: Integration with tools like Sentry or New Relic for error tracking and performance profiling in production.
    - **Logs**: Centralized logging (e.g., Cloud Logging) to retain audit trails.

## 5. Technology Stack
- **Backend**: **Laravel 12 (PHP)**.
    - *Pros*: Robust ecosystem, rapid development, native queue handling, ease of testing.
- **Frontend**: **Livewire + Tailwind CSS + Alpine.js (TALL Stack)**.
    - *Pros*: "No-API" complexity. Single repo. fast interactions without the overhead of a separate SPA (React/Vue) unless high interactivity is strictly required. Drag-and-drop uploads handled efficiently.
- **Database**: **MySQL**.
    - *Pros*: Reliable, relational data integrity for Users, Roles, and Document metadata.
    - *Search*: **Meilisearch** (Proposed enhancement) for full-text search on OCR'ed document content.

## 6. File Processing Architecture
- **Current Flow (MVP)**: Synchronous Upload -> Async Storage -> Synchronous AI Call.
- **Proposed Production Architecture**:
    1.  User Uploads File -> Stored in Temporary/Staging bucket.
    2.  Job Dispatched (`ProcessDocument`).
    3.  **Queue Worker** picks up job:
        -   Streams file to Google Drive (Permanent Storage).
        -   Calls Gemini API (Analysis).
        -   Updates Database with Results.
        -   Triggers Email Notification.
    - *Benefit*: User UI doesn't freeze. System handles parallel uploads via queue scaling.

## 7. Authentication Strategy
- **Web App**: Session-based Authentication (Cookies) via **Laravel Breeze**.
    - *Security*: CSRF protection, HttpOnly cookies.
- **Upload Agent / API** (Future): Token-based (Sanctum) or OAuth2 if 3rd party integrations are needed.

## 8. Database Choice
- **Relational (MySQL)** for structured data (Users, Permissions, Logs).
- **Blob Storage (Google Drive / S3)** for actual files.
- **Vector/Search DB** (Optional): If semantic search over document content is required, storing embeddings in `pgvector` or similar.
