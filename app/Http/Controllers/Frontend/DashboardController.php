<?php

namespace App\Http\Controllers\Frontend;

use View;
use Activity;

use App\User;
use App\Post;
use App\Topic;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $onlineUsersMinutes = config('custom.refresh_online_users_minutes');

            $visibleOnlineUsers = Activity::users($onlineUsersMinutes)
                ->join('users', 'sessions.user_id', 'users.id')
                ->where('is_invisible', false)
                ->get();

            $guestCount = Activity::guests()->count();
            $visibleOnlineUsersCount = $visibleOnlineUsers->count();
            $allOnlineUsersCount = Activity::users($onlineUsersMinutes)->count();

            View::share('postCount', Post::count());
            View::share('guestCount', $guestCount);
            View::share('topicCount', Topic::count());
            View::share('newestUser', User::newestUser());
            View::share('userCount', User::all()->count());
            View::share('visibleOnlineUsers', $visibleOnlineUsers);
            View::share('onlineUsersMinutes', $onlineUsersMinutes);
            View::share('is_admin', Auth::user()->is_admin ?? false);
            View::share('peopleOnline', $allOnlineUsersCount + $guestCount);
            View::share('visibleOnlineUsersCount', $visibleOnlineUsersCount);
            View::share('invisibleOnlineUsersCount', $allOnlineUsersCount - $visibleOnlineUsersCount);

            return $next($request);
        });
    }
}
