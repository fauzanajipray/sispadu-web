<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log as FacadesLog;
use Log;

class CheckIfAdmin
{
    /**
     * Checked that the logged in user is an administrator.
     *
     * --------------
     * VERY IMPORTANT
     * --------------
     * If you have both regular users and admins inside the same table, change
     * the contents of this method to check that the logged in user
     * is an admin, and not a regular user.
     *
     * Additionally, in Laravel 7+, you should change app/Providers/RouteServiceProvider::HOME
     * which defines the route where a logged in user (but not admin) gets redirected
     * when trying to access an admin route. By default it's '/home' but Backpack
     * does not have a '/home' route, use something you've built for your users
     * (again - users, not admins).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    private function checkIfUserIsAdmin($user)
    {
        // return ($user->is_admin == 1);
        $user = auth()->user();
        FacadesLog::info('----Checking if user is admin----', [
            'user_id' => $user->id ?? null,
            'position_id' => $user->position_id ?? null,
            'role' => $user->role ?? null,
            'email' => $user->email ?? null,
        ]);
        if ($user !== null) {
            FacadesLog::info('User is not null', [
                'user_id' => $user->id,
                'role' => $user->role,
                'position_id' => $user->position_id,
            ]);
            if ($user->role === 'superadmin') {
                FacadesLog::info('User is superadmin', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                ]);
                return true;
            } else if ($user->position_id !== null) {
                FacadesLog::info('User has a position', [
                    'user_id' => $user->id,
                    'position_id' => $user->position_id,
                ]);
                return true;
            }
            FacadesLog::info('User is not admin', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);
            return false;
        } else {
            FacadesLog::info('User is null');
            return false;
        }
    }

    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            return redirect()->guest(backpack_url('login'));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        if (!$this->checkIfUserIsAdmin(backpack_user())) {
            FacadesLog::info('----User is not admin, logging out----');
            backpack_auth()->logout();
            if (!($request->ajax() || $request->wantsJson())) {
                \Alert::error('These credentials do not match our records.')->flash();
            }
            return $this->respondToUnauthorizedRequest($request);
        } else {
            FacadesLog::info('----User is admin, allowing access----', [
                'user_id' => backpack_user()->id,
                'role' => backpack_user()->role,
            ]);
        }

        return $next($request);
    }
}
