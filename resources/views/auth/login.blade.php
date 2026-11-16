<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - WebotApp Apex Manufacturing ERP</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full antialiased bg-slate-950 text-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-500 text-slate-950 font-black text-2xl shadow-xl shadow-amber-500/20 mb-3">
                W
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center justify-center gap-2">
                <span>WebotApp</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-mono font-medium">ERP</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Enterprise Manufacturing & Shop Floor Execution System</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600"></div>

            <h2 class="text-base font-bold text-white mb-1">Sign in to your workplace</h2>
            <p class="text-xs text-slate-400 mb-6">Each role enters a customized operational dashboard</p>

            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Work Email Address</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', 'admin@apexerp.com') }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <span class="text-[11px] text-amber-400">Default: password</span>
                    </div>
                    <input type="password" name="password" id="password" required value="password" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-0">
                        <span>Remember device</span>
                    </label>
                    <span class="text-slate-500 text-[11px]">256-bit SSL Scoped</span>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                    <span>Access ERP Workspace</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Quick Demo Role Sign-In Pills -->
            <div class="mt-6 pt-5 border-t border-slate-800">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 text-center">
                    Quick Role Selector (Click to switch dashboard)
                </div>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <button type="button" onclick="fillRole('admin@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-amber-400 text-[10px]">Super Admin</div>
                        <div class="text-[10px] text-slate-400 truncate">Executive Command</div>
                    </button>
                    <button type="button" onclick="fillRole('production@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-blue-400 text-[10px]">Production Head</div>
                        <div class="text-[10px] text-slate-400 truncate">Shop Floor / MES</div>
                    </button>
                    <button type="button" onclick="fillRole('quality@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-purple-400 text-[10px]">Quality Inspector</div>
                        <div class="text-[10px] text-slate-400 truncate">IQC / IPQC / NCRs</div>
                    </button>
                    <button type="button" onclick="fillRole('inventory@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-emerald-400 text-[10px]">Inventory Manager</div>
                        <div class="text-[10px] text-slate-400 truncate">Stock Ledger & Bins</div>
                    </button>
                    <button type="button" onclick="fillRole('accounts@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-teal-400 text-[10px]">Finance Manager</div>
                        <div class="text-[10px] text-slate-400 truncate">GL, AP/AR & GST</div>
                    </button>
                    <button type="button" onclick="fillRole('plant.manager@apexerp.com')" class="px-2.5 py-1.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 rounded-lg text-left text-slate-200 transition">
                        <div class="font-bold text-orange-400 text-[10px]">Plant Manager</div>
                        <div class="text-[10px] text-slate-400 truncate">Plant Operations</div>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-[11px] text-slate-400">
            WebotApp Apex Manufacturing ERP • Commercial-Grade Open Source
        </div>
    </div>

    <script>
        function fillRole(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
