# SocialToast - Document Audit System

## Overview
SocialToast is a **Document Audit System** built with **Laravel 12** and the **TALL Stack** (Tailwind, Alpine.js, Laravel, Livewire). It features an AI-powered auditing workflow using **Google Gemini** to analyze documents (PDFs and images) and determine their validity based on an adaptable "auditor persona".

![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4e56a6?style=for-the-badge&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Google Gemini](https://img.shields.io/badge/Google_Gemini-8E75B2?style=for-the-badge&logo=google&logoColor=white)

## Features

-   **Authentication & Roles**:
    -   Secure login via Laravel Breeze.
    -   Role-based access control (Admin vs. User) using `spatie/laravel-permission`.
    -   **Admin**: Full access, including Pulse dashboard.
    -   **User**: Can upload and view their own audits.
    -   Registration is strictly disabled (invite-only/seeder based).

-   **AI Document Auditing**:
    -   Integration with **Google Gemini API** (using `gemini-flash-latest` model).
    -   Analysis of invoices, receipts, and identification documents.
    -   Personas defined in the database (`prompts` table).

-   **Modern UI/UX**:
    -   **Drag & Drop Upload**: Smooth file handling using Livewire + Alpine.js.
    -   **Real-time Feedback**: Progress bars and instant status updates.
    -   **Responsive Design**: Built `mobile-first` with Tailwind CSS.

-   **Email Notifications**:
    -   Automated emails sent via **MailPit** upon audit completion.
    -   Admins receive alerts for new uploads and audit results.

-   **Observability**:
    -   **Laravel Pulse**: Real-time performance and usage monitoring (Admin only).

## Installation & Setup

### Prerequisites
-   Docker & Docker Compose

### Initial Setup
1.  **Clone the repository**:
    ```bash
    git clone https://github.com/marcellopato/SocialToast.git
    cd SocialToast
    ```

2.  **Start the environment (Sail)**:
    ```bash
    ./vendor/bin/sail up -d
    ```

3.  **Install Dependencies**:
    ```bash
    ./vendor/bin/sail composer install
    ./vendor/bin/sail npm install && ./vendor/bin/sail npm run dev
    ```

4.  **Environment Configuration**:
    Copy `.env.example` to `.env` and set your keys:
    ```ini
    GEMINI_API_KEY=your_google_gemini_key
    ```

5.  **Database & Seeders**:
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

## Default Credentials

| Role      | Email                   | Password   |
| :-------- | :---------------------- | :--------- |
| **Admin** | `admin@socialtoast.com` | `password` |
| **User**  | `user@socialtoast.com`  | `password` |

## Access Points

| Service         | URL                                                        | Info               |
| :-------------- | :--------------------------------------------------------- | :----------------- |
| **Application** | [http://localhost:8085](http://localhost:8085)             | Main Dashboard     |
| **MailPit**     | [http://localhost:8028](http://localhost:8028)             | Email Testing      |
| **Pulse**       | [http://localhost:8085/pulse](http://localhost:8085/pulse) | Monitoring (Admin) |
