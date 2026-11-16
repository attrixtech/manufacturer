<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            if (!session()->has('current_company_id')) {
                session(['current_company_id' => $user->company_id]);
            }

            if (!session()->has('current_branch_id')) {
                $defaultBranch = $user->branches()->wherePivot('is_default', true)->first()
                    ?? $user->branches()->first()
                    ?? $user->branch;

                session(['current_branch_id' => $defaultBranch?->id ?? $user->branch_id]);
            }
        }

        return $next($request);
    }
}

