<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPrivilege
{
    public function handle(Request $request, Closure $next, string $module)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect('admin/signin');
        }

        $user = \App\Models\Users::find($userId);

        if (!$user) {
            return redirect('admin/signin');
        }

        $hasAccess = \App\Models\UserProfPrivileges::where('UserProfileID', $user->UserProfileID)
            ->join('lookup', 'userprofprivileges.UserPrivilegesID', '=', 'lookup.LookupID')
            ->where('lookup.LookupName', $module)
            ->exists();

        if (!$hasAccess) {
            return redirect('admin/index')->with('error', 'You do not have access to this module.');
        }

        return $next($request);
    }
}
