<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\LetterType;
use App\Http\Requests\StoreLetterTypeRequest;
use App\Http\Requests\UpdateLetterTypeRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
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

    public function departmentsIndex(Request $request)
    {
        $isManage = $request->boolean('manage') || $request->hasAny(['q','type','status','page']);

        if (!$isManage) {
            $q = Department::query()->where('is_active', true);
            if ($search = $request->get('search')) {
                $q->where('name','like',"%$search%")
                  ->orWhere('code','like',"%$search%");
            }
            return response()->json($q->orderBy('name')->get(['id','name','code','type']));
        }

        $query = Department::query()
            ->when($request->filled('q'), function($qq) use ($request){
                $s = $request->q;
                $qq->where(function($w) use ($s){
                    $w->where('name','like',"%$s%")
                      ->orWhere('code','like',"%$s%")
                      ->orWhere('description','like',"%$s%" );
                });
            })
            ->when($request->filled('type'), function($qq) use ($request){
                $qq->where('type', $request->type);
            })
            ->when($request->filled('status'), function($qq) use ($request){
                if (in_array($request->status, ['0','1'], true)) {
                    $qq->where('is_active', $request->status === '1');
                }
            })
            ->withCount([
                'lettersTo as in_count',
                'lettersFrom as out_count'
            ])
            ->orderBy('name');

        $departments = $query->paginate(15);
        return response()->json($departments);
    }

    public function departmentsShow(Department $department)
    {
        $department->loadCount(['lettersTo as in_count','lettersFrom as out_count']);
        return response()->json($department);
    }

    public function departmentsStore(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $department = Department::create($data);
        $department->loadCount(['lettersTo as in_count','lettersFrom as out_count']);
        return response()->json(['message'=>'Department created','data'=>$department], 201);
    }

    public function departmentsUpdate(UpdateDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();
        $department->update($data);
        $department->loadCount(['lettersTo as in_count','lettersFrom as out_count']);
        return response()->json(['message'=>'Department updated','data'=>$department]);
    }

    public function departmentsDestroy(Department $department)
    {
        $department->delete();
        return response()->json(['message'=>'Department deleted']);
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

    public function letterTypesIndex(Request $request)
    {
        $query = LetterType::query();
        if ($s = $request->get('q')) {
            $query->where(function($q) use ($s){
                                $q->where('name','like',"%$s%")
                                    ->orWhere('code','like',"%$s%")
                                    ->orWhere('description','like',"%$s%");
            });
        }
        if ($status = $request->get('status')) {
            if (in_array($status,['0','1'], true)) {
                $query->where('is_active', $status === '1');
            }
        }
        if ($category = $request->get('category')) {
            $query->where('description','like',"%$category%" );
        }
        $types = $query->orderBy('name')->paginate(15);
        return response()->json($types);
    }

    public function letterTypesShow(LetterType $letterType)
    {
        $letterType->loadCount('letters');
        $data = $letterType->toArray();
        $data['used'] = $letterType->letters_count;
        return response()->json($data);
    }

    public function letterTypesStore(StoreLetterTypeRequest $request)
    {
        $data = $request->validated();
        $lt = LetterType::create($data);
        $lt->loadCount('letters');
        $d = $lt->toArray();
        $d['used'] = $lt->letters_count;
        return response()->json(['message'=>'Letter type created','data'=>$d], 201);
    }

    public function letterTypesUpdate(UpdateLetterTypeRequest $request, LetterType $letterType)
    {
        $data = $request->validated();
        $letterType->update($data);
        $letterType->loadCount('letters');
        $d = $letterType->toArray();
        $d['used'] = $letterType->letters_count;
        return response()->json(['message'=>'Letter type updated','data'=>$d]);
    }

    public function letterTypesDestroy(LetterType $letterType)
    {
        $letterType->delete();
        return response()->json(['message'=>'Letter type deleted']);
    }
}
