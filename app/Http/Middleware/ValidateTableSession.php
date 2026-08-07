<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Table;
use Symfony\Component\HttpFoundation\Response;

class ValidateTableSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || $request->is('manager*') || $request->is('developer*') || $request->is('erest*') || $request->is('sanctum-login') || $request->is('api/login') || $request->is('api/admin*')) {
            return $next($request);
        }

        if (auth()->check()) {
            return $next($request);
        }

        $tableToken = $request->query('masa') ?? $request->query('table') ?? $request->input('table_token') ?? $request->input('table');
        
        if (!$tableToken) {
            $tableToken = $request->input('table_number');
        }

        if ($tableToken) {
            $table = Table::where('token', $tableToken)->first();
            if (!$table) {
                $table = Table::where('name', $tableToken)->first();
            }

            if ($table) {
                $token = $request->query('token') ?? $request->input('token');
                
                if ($token) {
                    if ($table->session_token === $token && $table->session_expires_at && now()->lessThan($table->session_expires_at)) {
                        session([
                            'active_table_id' => $table->id,
                            'table_session_token_' . $table->id => $token
                        ]);
                        $table->session_expires_at = now()->addMinutes(60);
                        $table->save();
                        return $next($request);
                    }
                    abort(403, 'Gecersiz veya suresi dolmus oturum.');
                }

                $savedToken = session('table_session_token_' . $table->id);
                if ($savedToken && $table->session_token === $savedToken && $table->session_expires_at && now()->lessThan($table->session_expires_at)) {
                    session(['active_table_id' => $table->id]);
                    $table->session_expires_at = now()->addMinutes(60);
                    $table->save();
                    return $next($request);
                }

                abort(403, 'Bu masa icin aktif bir oturum bulunmamaktadir.');
            }
        }

        $activeTableId = session('active_table_id');
        if ($activeTableId) {
            $table = Table::find($activeTableId);
            if ($table) {
                $savedToken = session('table_session_token_' . $table->id);
                if ($savedToken && $table->session_token === $savedToken && $table->session_expires_at && now()->lessThan($table->session_expires_at)) {
                    $table->session_expires_at = now()->addMinutes(60);
                    $table->save();
                    return $next($request);
                }
                session()->forget(['active_table_id', 'table_session_token_' . $table->id]);
            }
        }

        if ($request->is('/') && !$request->has('masa') && !$request->has('table')) {
            return $next($request);
        }

        abort(403, 'Oturum dogrulanamadi.');
    }
}
