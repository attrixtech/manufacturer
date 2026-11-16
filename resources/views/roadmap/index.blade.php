<x-app-layout>
    <x-slot:title>Master Development Roadmap & Execution Phases</x-slot:title>

    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-800">
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>12-Phase Development Roadmap</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 font-mono">Enterprise Scope</span>
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Execution roadmap derived directly from todo.txt specification</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400 font-mono">Current Status:</span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Phase 1 Deployed
                </span>
            </div>
        </div>

        @php
            $phases = [
                [
                    'num' => 1,
                    'title' => 'Architecture, Core Security & Foundation',
                    'status' => 'completed',
                    'deliverables' => [
                        'Application architecture & modular structure',
                        'Database ER diagram & entity design',
                        'Multi-company & multi-branch tenancy schema',
                        'User management with Spatie RBAC & 9 manufacturing roles',
                        'Enterprise Tailwind CSS Blade layout with sidebar & context switcher',
                        'Immutable activity & audit logging system',
                        'Demo company & plant seeders',
                    ]
                ],
                [
                    'num' => 2,
                    'title' => 'Master Data Management (MDM)',
                    'status' => 'ready',
                    'deliverables' => [
                        'Product Master (Raw, Semi-Finished, Finished, Consumable, Scrap)',
                        'Product Categories, Attributes & Variants',
                        'Units of Measure (UOM) & Conversion Matrices',
                        'Supplier & Customer Masters with GSTIN, PAN & Terms',
                        'Warehouses, Zones, Racks & Bins',
                        'Work Centers & Machine Asset Register with capacities & hour rates',
                    ]
                ],
                [
                    'num' => 3,
                    'title' => 'Perpetual Inventory & Stock Ledger',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Immutable Stock Ledger with source document linking',
                        'Real-time Stock Balances per Warehouse & Bin',
                        'Batch / Lot Tracking with Expiry & Retest Dates',
                        'Serial Number Registry for serialized parts',
                        'Inter-Warehouse & Inter-Branch Stock Transfers',
                        'Stock Adjustments & Physical Inventory Reconciliation (Cycle Counts)',
                        'Stock Valuation Engine (FIFO & Weighted Average Costing)',
                    ]
                ],
                [
                    'num' => 4,
                    'title' => 'Procurement & Supplier Management',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Purchase Requisitions (PR) with Approval Workflows',
                        'Request for Quotations (RFQ) & Vendor Comparative Statement',
                        'Purchase Orders (PO) with delivery scheduling & terms',
                        'Goods Receipt Note (GRN) with Quality Quarantine routing',
                        '3-Way Matching (PO vs GRN vs Vendor Invoice)',
                        'Purchase Returns & Debit Notes',
                        'Supplier Ledger & Payment Allocations',
                    ]
                ],
                [
                    'num' => 5,
                    'title' => 'Engineering: Bill of Materials & Routings',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Multi-level BOM Builder with Engineering Change Orders (ECO)',
                        'BOM Component Scrap Factor, Co-products & By-products',
                        'Standard Cost Rollups (Raw Material + Machine Overhead + Direct Labor)',
                        'Routing Operations, Sequences, Setup & Run Times',
                        'Work Center Scheduling & Machine Capacity Profiles',
                    ]
                ],
                [
                    'num' => 6,
                    'title' => 'Production Planning & Material Requirements Planning (MRP)',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Master Production Schedule (MPS) from Sales Orders & Forecasts',
                        'MRP Run Engine (Gross to Net explosion, Lead time offset, Safety stock)',
                        'Rough-Cut Capacity Planning (RCCP)',
                        'Actionable Purchase & Production Order Recommendations',
                    ]
                ],
                [
                    'num' => 7,
                    'title' => 'Manufacturing Execution System (MES)',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Work Orders generated from MPS or Direct Entry',
                        'Material Reservation & Staging to WIP bins',
                        'Production Material Issue & Floor Return Notes',
                        'Digital Job Cards & Machine Operator Shift Logging',
                        'Work In Progress (WIP) tracking across operation steps',
                        'Finished Goods Production Receipts & Scrap/Rework Accounting',
                    ]
                ],
                [
                    'num' => 8,
                    'title' => 'Quality Control (QA/QC) & Plant Maintenance',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Inspection Parameter Templates (Quantitative & Qualitative ranges)',
                        'Incoming QC (IQC) at GRN stage',
                        'In-Process QC (IPQC) along routing stages',
                        'Final QC (FQC) before finished goods release',
                        'Non-Conformance Reports (NCR) & Corrective Actions (CAPA)',
                        'Preventive & Breakdown Machine Maintenance with MTBF & MTTR',
                    ]
                ],
                [
                    'num' => 9,
                    'title' => 'Sales, Order Fulfillment & Dispatch',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Sales Enquiries, RFQs & Quotations with margin calculators',
                        'Sales Orders with Available-to-Promise (ATP) inventory check',
                        'Delivery Notes / Challans with E-Way Bill staging',
                        'GST Compliant Tax Invoices (B2B, B2C, Export)',
                        'Customer Ledger & Payment Receipts',
                    ]
                ],
                [
                    'num' => 10,
                    'title' => 'Financial Accounting & Cost Management',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Hierarchical Chart of Accounts (Assets, Liabilities, Equity, Revenue, Expense)',
                        'Automated Real-Time Journal Entry Engine (Balanced Debits & Credits)',
                        'Accounts Payable & Accounts Receivable Ageing',
                        'Actual vs Standard Production Cost Variance Analysis',
                        'GST Summary Reports (GSTR-1, GSTR-3B) and Financial Statements',
                    ]
                ],
                [
                    'num' => 11,
                    'title' => 'Advanced Features & Industry Workflows',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'Subcontracting & Job Work (Outward/Inward challans & reconciliation)',
                        'Barcode / QR Code generation & mobile scanning',
                        'Centralized Document Management (Drawings, MSDS, Test Reports)',
                        'Automated Email & In-App Alert Notifications',
                    ]
                ],
                [
                    'num' => 12,
                    'title' => 'Automated Testing, Hardening & Deployment',
                    'status' => 'upcoming',
                    'deliverables' => [
                        'PHPUnit / Pest Feature & Integration Tests',
                        'Atomic stock movement & financial balance verification tests',
                        'Database indexing & query optimization for high volume transactions',
                        'Automated backup & disaster recovery protocols',
                    ]
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($phases as $p)
                <div class="p-5 rounded-2xl bg-slate-900 border {{ $p['status'] === 'completed' ? 'border-emerald-500/40 bg-emerald-950/10' : ($p['status'] === 'ready' ? 'border-amber-500/40' : 'border-slate-800') }} relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-mono text-amber-500 font-bold uppercase tracking-wider text-[11px]">Phase {{ $p['num'] }}</span>
                            @if($p['status'] === 'completed')
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/20 text-emerald-400 font-bold border border-emerald-500/30 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    COMPLETED
                                </span>
                            @elseif($p['status'] === 'ready')
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-500/20 text-amber-400 font-bold border border-amber-500/30 flex items-center gap-1">
                                    READY TO COMMENCE
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-800 text-slate-400 font-medium">
                                    PLANNED
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm font-bold text-white mb-3">{{ $p['title'] }}</h3>

                        <ul class="space-y-1.5 text-xs text-slate-300">
                            @foreach($p['deliverables'] as $item)
                                <li class="flex items-start gap-2">
                                    <svg class="w-3.5 h-3.5 {{ $p['status'] === 'completed' ? 'text-emerald-400' : 'text-slate-500' }} shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $p['status'] === 'completed' ? 'M5 13l4 4L19 7' : 'M9 5l7 7-7 7' }}" />
                                    </svg>
                                    <span class="leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

