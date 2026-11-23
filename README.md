# WebotApp Apex Manufacturing ERP

<p align="center">
  <img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License: MIT">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4.svg" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-11%2F12-FF2D20.svg" alt="Laravel">
  <img src="https://img.shields.io/badge/Tailwind-CSS%204-38B2AC.svg" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Livewire-v4-FB70A9.svg" alt="Livewire">
  <img src="https://img.shields.io/badge/Architecture-Multi--Company%20MES-amber.svg" alt="Architecture">
</p>

Enterprise-grade Manufacturing Execution System (MES) & Discrete/Batch ERP with real-time shop floor dispatching, multi-level Bill of Materials (BOM), perpetual stock ledger, quality gates (IQC, IPQC, FQC), procurement, and machine telemetry.

---

## 🛠️ Modules & Capabilities

- 🏭 **Shop Floor Execution (MES)**: Real-time work order dispatching, yield tracking, scrap recording, and operation progress monitoring.
- ⚙️ **Bill of Materials (BOM) & Routings**: Multi-level component explosion, versioning, operation routing steps, and costing rollups.
- 📦 **Products & Item Master**: Cataloging for Finished Goods, Semi-Finished Blanks, Raw Materials, and Tooling.
- 📊 **Perpetual Stock Ledger**: Multi-warehouse balance tracking with batch/lot genealogy.
- 🛡️ **Quality Control (QA/QC)**: Inspection queues (IQC, IPQC, FQC) and Non-Conformance Reports (NCR) with CAPA tracking.
- 🛒 **Procurement & PO**: Purchase order management, line item pricing, and vendor fulfillment.
- 💰 **General Ledger & Accounting**: Double-entry chart of accounts and general journal vouchers.
- ⚡ **Machine Telemetry & Sentinel**: Live machine diagnostics (spindle load, coolant pressure, tool life) and downtime incident logging.
- 🏢 **Multi-Company & Plants**: Support for multiple corporate entities, manufacturing plants, and departments.

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js & npm
- MySQL 8.0+

### Setup Commands
```bash
# Clone the repository
git clone git@github.com:webotapp-academy/WebotApp-Apex-Manufacturing-ERP.git
cd WebotApp-Apex-Manufacturing-ERP

# Install dependencies
composer install
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database migration and seeding
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the application server
php artisan serve
```

The application will run at `http://127.0.0.1:8000`.

---

## 📄 License

This software is released under the **MIT License**.

Copyright (c) 2026 WebotApp Academy.
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the software, subject to the standard MIT conditions.
