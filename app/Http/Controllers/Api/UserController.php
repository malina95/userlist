<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Tinify\Tinify;
use function Tinify\fromFile;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('position:id,name')->paginate(6);

        $array = [];
        $array['success'] = true;
        $array['page'] = $users->currentPage();
        $array['total_pages'] = $users->lastPage();
        $array['total_users'] = $users->total();
        $array['count'] = $users->count();
        $array['links']['prev_url'] = $users->previousPageUrl();
        $array['links']['next_url'] = $users->nextPageUrl();
        $array['users'] = [];

        foreach ($users as $user) {
            $userArray = $user->toArray();
            $position = $user->position()->get();

            if ($position) {
                $position = $position->toArray();

                if (isset($position[0]['name'])) {
                    $userArray['position'] = $position[0]['name'];
                }
            }

            $userArray['registration_timestamp'] = $user->created_at;
            $userArray['photo'] = url('/') . '/images/' . $user->created_at;

            $array['users'][] = $userArray;
        }

        return $array;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->hasHeader('Token')) {
            return response()->json([
                'success' => false,
                'message' => 'The token is required.'
            ], 401);
        }

        $token = Token::where(['token' => $request->header('Token')])->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'The token not found.'
            ], 401);
        }

        if ($token->is_used > 0) {
            return response()->json([
                'success' => false,
                'message' => 'The token expired.'
            ], 401);
        }

        $token->is_used = 1;
        $token->save();

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:60',
            'email' => 'required|email|unique:users|min:2|max:100',
            'phone' => 'required|regex:/^[\+]{0,1}380([0-9]{9})$/',
            'position_id' => 'required|min:1',
            'photo' => [
                'required',
                File::image()
                    ->max(5 * 1024),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'fails' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $checkUserExist = DB::table('users')
            ->where(['email' => $validated['email']])
            ->orWhere(['phone' => $validated['phone']])
            ->exists();
        if ($checkUserExist) {
            return response()->json([
                'success' => false,
                'message' => 'User with this phone or email already exist'
            ], 409);
        }

        $file = $request->file('photo');
        $destinationPath = 'uploads';
        $destinationShortPath = 'uploads/short/';
        $name = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $name);

        Tinify::setKey(env('TINYPNG_KEY'));
        $source = fromFile($destinationPath . '/' . $name);
        $resized = $source->resize([
            "method" => "fit",
            "width" => 70,
            "height" => 70
        ]);
        $resized->toFile($destinationShortPath . $name);

        $validated['photo'] = $name;

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'message' => 'New user successfully registered'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $array = [];
        $array['success'] = true;
        $array['user'] = $user;
        $position = $user->position()->get();

        if ($position) {
            $position = $position->toArray();
            if (isset($position[0]['name'])) {
                $array['user']['position'] = $position[0]['name'];
            }
        }

        return $array;
    }
}
