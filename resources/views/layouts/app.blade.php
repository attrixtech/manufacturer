<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'WebotApp Apex Manufacturing ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="h-full antialiased bg-slate-50 text-slate-800">
    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Sidebar Navigation (Light Mode) -->
        <aside class="w-full lg:w-72 bg-white border-r border-slate-200 flex flex-col shrink-0 select-none shadow-xs">
            <!-- Brand / Logo -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200 bg-white">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-500 flex items-center justify-center font-extrabold text-white text-xl shadow-md shadow-amber-500/20">
                        W
                    </div>
                    <div>
                        <div class="text-base font-bold tracking-tight text-slate-900 flex items-center gap-1.5">
                            WebotApp <span class="text-xs px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 font-mono font-semibold">ERP</span>
                        </div>
                        <div class="text-[10px] text-slate-500 tracking-wider uppercase font-semibold">Apex Manufacturing MES</div>
                    </div>
                </a>
            </div>

            <!-- Active Company & Branch Context Card -->
            @php
                $activeCompany = \App\Models\Company::find(session('current_company_id')) ?? auth()->user()->company;
                $activeBranch = \App\Models\Branch::find(session('current_branch_id')) ?? auth()->user()->branch;
                $allCompanies = \App\Models\Company::where('is_active', true)->get();
                $allBranches = $activeCompany ? $activeCompany->branches()->where('is_active', true)->get() : collect();
                $userRole = auth()->user()->roles->first()?->name ?? 'User';
            @endphp
            <div class="p-3 mx-3 mt-4 rounded-xl bg-slate-50 border border-slate-200">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-slate-500 uppercase tracking-wider font-bold text-[10px]">Active Entity</span>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                    </span>
                </div>
                <div class="font-bold text-xs text-slate-900 truncate" title="{{ $activeCompany?->name }}">
                    {{ $activeCompany?->name ?? 'Apex Precision Pvt Ltd' }}
                </div>
                <div class="text-[11px] text-amber-600 font-medium flex items-center gap-1 mt-0.5 truncate">
                    <svg class="w-3 h-3 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ $activeBranch?->name ?? 'Pune Plant 01' }}
                </div>
            </div>

            <!-- Role Badge Banner in Sidebar -->
            <div class="mx-3 mt-2 px-3 py-1.5 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between">
                <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Your Workspace</span>
                <span class="text-[11px] font-bold text-amber-800 truncate">{{ $userRole }}</span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-xs font-medium">
                <!-- Group 1: Core Operations (Always visible) -->
                <div>
                    <div class="px-3 mb-2 text-[10px] uppercase font-bold tracking-wider text-slate-400">Navigation</div>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>{{ $userRole === 'Machine Operator' ? 'My Machine Terminal' : 'Operational Dashboard' }}</span>
                        </a>

                        @if(auth()->user()->can('reports.view') || in_array($userRole, ['Super Administrator', 'Plant Manager']))
                            <a href="{{ route('roadmap.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('roadmap.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Roadmap & Execution</span>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">12 Phases</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Group 2: Enterprise Administration (Only for Super Admin and Plant Manager / users with permissions) -->
                @if(auth()->user()->hasAnyRole(['Super Administrator', 'Plant Manager']) || auth()->user()->can('companies.view'))
                    <div>
                        <div class="px-3 mb-2 text-[10px] uppercase font-bold tracking-wider text-slate-400">Enterprise Administration</div>
                        <div class="space-y-1">
                            <a href="{{ route('companies.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('companies.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Companies & Plants</span>
                            </a>

                            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Users, Roles & RBAC</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Group 3: Role-Specific Operational Modules -->
                <div>
                    <div class="px-3 mb-2 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                        {{ $userRole === 'Machine Operator' ? 'Machine Functions' : 'Permitted Operations' }}
                    </div>
                    <div class="space-y-1">

                        <!-- Production Modules -->
                        @if(auth()->user()->can('work_orders.view') || auth()->user()->hasRole('Machine Operator'))
                            <a href="{{ route('work-orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('work-orders.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('work-orders.*') ? 'text-white' : 'text-amber-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>{{ $userRole === 'Machine Operator' ? 'Active Job Cards' : 'Work Orders & MES' }}</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('work-orders.*') ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700 border border-blue-200' }} px-1.5 py-0.5 rounded font-mono font-bold">MES</span>
                            </a>
                        @endif

                        <!-- BOM / Routing Modules -->
                        @if(auth()->user()->can('bom.view'))
                            <a href="{{ route('bom.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bom.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('bom.*') ? 'text-white' : 'text-cyan-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    </svg>
                                    <span>BOM & Routings</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('bom.*') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }} px-1.5 py-0.5 rounded font-mono font-medium">ENG</span>
                            </a>
                        @endif

                        <!-- Inventory & Stock Modules -->
                        @if(auth()->user()->can('inventory.view'))
                            <a href="{{ route('inventory.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('inventory.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('inventory.*') ? 'text-white' : 'text-emerald-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                    </svg>
                                    <span>Stock Ledger & Bins</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('inventory.*') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }} px-1.5 py-0.5 rounded font-mono font-medium">SCM</span>
                            </a>
                        @endif

                        <!-- Quality Assurance Modules -->
                        @if(auth()->user()->can('quality.view'))
                            <a href="{{ route('quality.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('quality.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('quality.*') ? 'text-white' : 'text-purple-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span>Quality & Inspections</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('quality.*') ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-700 border border-purple-200' }} px-1.5 py-0.5 rounded font-mono font-bold">QA/QC</span>
                            </a>
                        @endif

                        <!-- Procurement Modules -->
                        @if(auth()->user()->can('procurement.view'))
                            <a href="{{ route('procurement.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('procurement.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('procurement.*') ? 'text-white' : 'text-orange-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Procurement & PO</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('procurement.*') ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }} px-1.5 py-0.5 rounded font-mono font-medium">PUR</span>
                            </a>
                        @endif

                        <!-- Finance & Costing Modules -->
                        @if(auth()->user()->can('accounting.view'))
                            <a href="{{ route('accounting.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('accounting.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('accounting.*') ? 'text-white' : 'text-emerald-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>General Ledger & GST</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('accounting.*') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }} px-1.5 py-0.5 rounded font-mono font-bold">FIN</span>
                            </a>
                        @endif

                        <!-- Maintenance & Breakdown Modules -->
                        @if(auth()->user()->can('maintenance.view') || auth()->user()->hasRole('Machine Operator'))
                            <a href="{{ route('maintenance.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('maintenance.*') ? 'bg-amber-500 text-white font-bold shadow-sm shadow-amber-500/20' : 'text-slate-700 hover:bg-slate-100' }}">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('maintenance.*') ? 'text-white' : 'text-rose-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Breakdown & Downtime</span>
                                </div>
                                <span class="text-[9px] {{ request()->routeIs('maintenance.*') ? 'bg-white/20 text-white' : 'bg-rose-50 text-rose-700 border border-rose-200' }} px-1.5 py-0.5 rounded font-mono font-bold">ALERT</span>
                            </a>
                        @endif

                    </div>
                </div>
            </nav>

            <!-- Bottom User Profile Bar -->
            <div class="p-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-amber-100 border border-amber-300 flex items-center justify-center font-bold text-xs text-amber-800 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-amber-700 font-semibold truncate">{{ $userRole }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-200 transition" title="Logout">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area (Light Mode) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto bg-slate-50">
            <!-- Top Navbar -->
            <header class="h-16 bg-white/90 backdrop-blur border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <!-- Search & Context Selectors -->
                <div class="flex items-center gap-4">
                    <div class="relative w-64 lg:w-80">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" placeholder="Quick search (WO#, Batch#, Part#, PO#)..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                    </div>

                    @if(auth()->user()->hasAnyRole(['Super Administrator', 'Plant Manager']))
                        <!-- Company Switcher Dropdown Form -->
                        <form action="{{ route('companies.switch') }}" method="POST" class="hidden sm:flex items-center gap-1.5">
                            @csrf
                            <label for="company_id" class="text-[11px] text-slate-500 font-medium">Company:</label>
                            <select name="company_id" id="company_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs rounded-lg px-2.5 py-1 text-slate-800 font-medium focus:outline-none focus:border-amber-500">
                                @foreach($allCompanies as $comp)
                                    <option value="{{ $comp->id }}" {{ $activeCompany?->id == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <!-- Branch Switcher Dropdown Form -->
                        <form action="{{ route('branches.switch') }}" method="POST" class="hidden md:flex items-center gap-1.5">
                            @csrf
                            <label for="branch_id" class="text-[11px] text-slate-500 font-medium">Plant:</label>
                            <select name="branch_id" id="branch_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-xs rounded-lg px-2.5 py-1 text-amber-700 focus:outline-none focus:border-amber-500 font-bold">
                                @foreach($allBranches as $br)
                                    <option value="{{ $br->id }}" {{ $activeBranch?->id == $br->id ? 'selected' : '' }}>
                                        {{ $br->name }} ({{ strtoupper($br->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <div class="hidden sm:flex items-center gap-1.5 text-xs text-slate-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span class="font-medium">{{ $activeBranch?->name ?? 'Plant Station' }}</span>
                        </div>
                    @endif
                </div>

                <!-- Right Quick Indicators -->
                <div class="flex items-center gap-3">
                    <div class="hidden lg:flex items-center gap-2 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-[11px] text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Role: <strong class="text-amber-700">{{ $userRole }}</strong></span>
                    </div>

                    <div class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold flex items-center gap-1.5">
                        <span>WebotApp MES</span>
                    </div>
                </div>
            </header>

            <!-- Alerts Banner -->
            <main class="flex-1 p-6 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold shadow-xs">
                        <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold shadow-xs">
                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
