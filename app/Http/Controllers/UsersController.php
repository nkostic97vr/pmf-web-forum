<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Hash;
use Validator;

use App\Post;
use App\User;
use App\Topic;

use Illuminate\Validation\Rule;

class UsersController extends Controller {

    public function index() {
        $request = request();

        /*
        |--------------------------------------------------------------------------
        | Get Valid or Correct Bad Input
        |--------------------------------------------------------------------------
        */

        $cols = ['username', 'about', 'registered_at', 'post_count'];
        $max = (int)config('custom.pagination.max');
        $step = (int)config('custom.pagination.step');

        $validator = Validator::make($request->all(), [
            'perPage' => "integer|between:0,{$max}",
            'sort_column' => Rule::in($cols),
            'sort_order' => Rule::in(['asc', 'desc']),
            'search_column' => Rule::in($cols),
        ]);

        $errors = $validator->errors();

        $perPage =  $request->has('perPage') && !$errors->has('perPage') ? (int)$request->perPage : $step;
        $sortColumn = $request->has('sort_column') && !$errors->has('sort_column') ? $request->sort_column : 'username';
        $sortOrder = $request->has('sort_order') && !$errors->has('sort_order') ? $request->sort_order : 'asc';
        $searchColumn = $request->has('search_column') && !$errors->has('search_column') ? $request->search_column : 'title';

        if ($remainder = $perPage % $step) {
            $perPage = ($perPage - $remainder) ?: $step;
            $errors->add('perPage', '');
        }

        if ($errors->any()) {
            $queries = [
                'perPage' => $perPage,
                'sort_column' => $sortColumn,
                'sort_order' => $sortOrder,
            ];

            return redirect(request()->fullUrlWithQuery($queries));
        }

        /*
        |--------------------------------------------------------------------------
        | Build Query And Fetch Data
        |--------------------------------------------------------------------------
        */

        $users = User::query()
            ->select('username', 'registered_at', 'about', DB::raw('COUNT(posts.id) AS post_count'))
            ->join('profiles', 'users.id', 'profiles.user_id')
            ->leftJoin('posts', 'users.id', 'posts.user_id')
            ->groupBy('username', 'registered_at', 'about')
            ->orderBy($sortColumn, $sortOrder);

        if ($perPage) {
            $users = $users->paginate($perPage);
        } else {
            $users = $users->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        return view('website.users.index')
            ->with(compact('users', 'perPage', 'step', 'max', 'sortColumn', 'sortOrder'));
    }

    public function index_admin() {

    }

    public function show($username) {
        if (Auth::check()) {
            $user = User::where('username', $username)->firstOrFail();
            return view('website.users.show')
                ->with('user', $user)
                ->with('profile', $user->profile);
        } else {
            return alert_redirect(url()->previous(), 'info', __('auth.must-login'));
        }
    }

    public function edit($username) {
        if (Auth::check()) {
            $user = User::where('username', $username)->firstOrFail();
            if (Auth::id() == $user->id || Auth::user()->is_master) {
                return view('website.users.edit')
                    ->with('user', $user)
                    ->with('profile', $user->profile);
            }
            return redirect(route_user_show($user));
        } else {
            return alert_redirect(route(url()->previous()), 'info', __('auth.must-login'));
        }
    }

    public function update($username) {
        $errors = [];
        $request = request();

        $user = User::where('username', $username)->firstOrFail();
        $profile = $user->profile;

        $user->is_invisible = $request->is_invisible;

        if ($user->email !== $request->email || is_not_empty($request->password) || is_not_empty($request->password_confirm)) {
            if (is_empty($request->password_current)) {
                $errors['password_current'] = 'Niste upisali trenutnu šifru.';
            } elseif (!Hash::check($request->password_current, $user->password)) {
                $errors['password_current'] = 'Upisali ste pogrešnu šifru.';
            } else {
                if ($user->email !== $request->email) {
                    $user->to_logout = true;
                    $user->email = $request->email;
                    $user->email_token = str_random(30);
                    $user->notify(new ConfirmEmail($user->email_token));
                }

                if (is_not_empty($request->password) || is_not_empty($request->password_confirm)) {
                    if ($request->password !== $request->password_confirm) {
                        $errors['password_matching'] = 'Lozinke se ne poklapaju.';
                    } elseif (!isset($errors['password_current'])) {
                        $user->password = Hash::make($request->password);
                        $user->to_logout = true;
                    }
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $profile->about = $request->about;
        $profile->birthday_on = $request->birthday_on;
        $profile->sex = $request->sex;
        $profile->job = $request->job;
        $profile->name = $request->name;
        $profile->residence = $request->residence;
        $profile->birthplace = $request->birthplace;
        $profile->avatar = $request->avatar;
        $profile->signature = $request->signature;

        $user->save();
        $profile->save();

        return alert_redirect(route_user_show($user), 'success', __('db.updated'));
    }

    public function ban($username) {
        // $user = User::where('username', $username)->firstOrFail();
        // $user->is_banned = !$user->is_banned;
        // $user->save();
        // return redirect()->back();
    }

}