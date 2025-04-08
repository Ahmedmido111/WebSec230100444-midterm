<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Artisan;

use App\Http\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller {

	use ValidatesRequests;

    public function list(Request $request) {
        if(!auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }
        $query = User::select('*');
        $query->when($request->keywords, 
        fn($q)=> $q->where("name", "like", "%$request->keywords%"));
        $users = $query->get();
        return view('users.list', compact('users'));
    }

	public function register(Request $request) {
        return view('users.register');
    }

    public function doRegister(Request $request) {
        try {
            $this->validate($request, [
                'name' => ['required', 'string', 'min:5'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);
        }
        catch(\Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors('Invalid registration information.');
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->credit = 0; // Set initial credit
        $user->save();

        // Assign customer role to new users
        $user->assignRole('customer');

        return redirect('/')->with('success', 'Registration successful! Please login.');
    }

    public function login(Request $request) {
        return view('users.login');
    }

    public function doLogin(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->back()->withInput($request->input())->withErrors('Invalid login information.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Auth::logout();
            return redirect()->back()->withErrors('User not found.');
        }

        Auth::setUser($user);
        return redirect('/')->with('success', 'Welcome back!');
    }

    public function doLogout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You have been logged out.');
    }

    public function profile(Request $request, User $user = null) {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            abort(404, 'User not found');
        }

        if(auth()->id() != $user->id && !auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }

        // Get direct permissions
        $permissions = $user->getDirectPermissions();
        
        // Get permissions from roles
        $rolePermissions = $user->getPermissionsViaRoles();
        
        // Merge all permissions
        $allPermissions = $permissions->merge($rolePermissions)->unique('id');

        return view('users.profile', compact('user', 'allPermissions'));
    }

    public function edit(Request $request, User $user = null) {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            abort(404, 'User not found');
        }

        if(auth()->id() != $user->id && !auth()->user()->hasPermissionTo('edit_users')) {
            abort(401);
        }

        $roles = collect();
        foreach(Role::all() as $role) {
            $role->taken = $user->hasRole($role->name);
            $roles->push($role);
        }

        $permissions = collect();
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach(Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions->push($permission);
        }

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user) {
        if (!$user) {
            abort(404, 'User not found');
        }

        if(auth()->id() != $user->id && !auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if(auth()->user()->hasPermissionTo('admin_users')) {
            $user->syncRoles($request->roles ?? []);
            $user->syncPermissions($request->permissions ?? []);
            Artisan::call('cache:clear');
        }

        return redirect(route('profile', ['user' => $user->id]))->with('success', 'Profile updated successfully.');
    }

    public function delete(Request $request, User $user) {
        if (!$user) {
            abort(404, 'User not found');
        }

        if(!auth()->user()->hasPermissionTo('delete_users')) {
            abort(401);
        }

        $user->delete();
        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }

    public function editPassword(Request $request, User $user = null) {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            abort(404, 'User not found');
        }

        if(auth()->id() != $user->id && !auth()->user()->hasPermissionTo('edit_users')) {
            abort(401);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user) {
        if (!$user) {
            abort(404, 'User not found');
        }

        if(auth()->id() == $user->id) {
            $this->validate($request, [
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);

            if(!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {
                Auth::logout();
                return redirect('/')->withErrors('Current password is incorrect.');
            }
        }
        else if(!auth()->user()->hasPermissionTo('edit_users')) {
            abort(401);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return redirect(route('profile', ['user' => $user->id]))->with('success', 'Password updated successfully.');
    }
} 