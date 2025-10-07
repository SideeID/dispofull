<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function pengguna()
    {
        $users = User::with('department')->latest()->paginate(15);
        return view('pages.admin.pengguna.index', compact('users'));
    }

    public function departemen()
    {
        return view('pages.admin.departemen.index');
    }

    public function jenisSurat()
    {
        return view('pages.admin.jenis-surat.index');
    }

    public function monitoring()
    {
        return view('pages.admin.monitoring.index');
    }

    /* =================== User CRUD (API-like endpoints for AJAX) =================== */

    public function departmentsIndex(Request $request)
    {
        $q = Department::query()->where('is_active', true);
        if ($search = $request->get('search')) {
            $q->where('name','like',"%$search%");
        }
        return response()->json($q->orderBy('name')->get(['id','name','code','type']));
    }

    public function usersIndex(Request $request)
    {
        $query = User::with('department');
        if ($s = $request->get('search')) {
            $query->where(function($q) use ($s){
                $q->where('name','like',"%$s%")
                  ->orWhere('email','like',"%$s%")
                  ->orWhere('nip','like',"%$s%");
            });
        }
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        $users = $query->paginate(20);
        return response()->json($users);
    }

    public function usersShow(User $user)
    {
        $user->load('department');
        return response()->json($user);
    }

    public function usersStore(StoreUserRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function() use (&$data){
            $data['password'] = Hash::make($data['password']);
            if (empty($data['username'])) {
                $data['username'] = $this->generateUsername($data['name']);
            }
            if (empty($data['status'])) {
                $data['status'] = 'active';
            }
            $data = User::create($data)->toArray();
        });
        return response()->json(['message' => 'User created','data'=>$data], 201);
    }

    public function usersUpdate(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['message' => 'User updated','data'=>$user->fresh('department')]);
    }

    public function usersDestroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    private function generateUsername(string $name): string
    {
        $base = strtolower(preg_replace('/[^a-z0-9]+/i','', substr($name,0,12)));
        $username = $base;
        $i = 1;
        while(User::where('username',$username)->exists()) {
            $username = $base . $i;
            $i++;
        }
        return $username ?: 'user'.time();
    }
}
