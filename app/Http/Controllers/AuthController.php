<?php
/**
 * Created by PhpStorm.
 * Filename: AuthController.php
 * User: falconerialta@gmail.com
 * Date: 2019-02-25
 * Time: 11:44
 */

namespace App\Http\Controllers;

use Validator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function register(Request $request) {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => '422',
                    'error' => $validator->errors(),
                ], 422
            );
        }

        try {
            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));

            $newUser = User::create(
                [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'api_token' => ''
                ]
            );

            if ($newUser) {
                return response()->json(
                    [
                        'data' => $newUser
                    ], 200
                );
            } else {
                return response()->json(
                    [
                        'status' => '401',
                        'error' => 'Registration Failed!',
                    ], 401
                );
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'status' => '500',
                    'error' => $ex->getMessage(),
                ], 500
            );
        }
    }

    public function login(Request $request) {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => '422',
                    'error' => $validator->errors(),
                ], 422
            );
        }


        try {
            $email = $request->input('email');
            $password = $request->input('password');

            $user = User::where('email', $email)->first();
            if ($user && $user->count() > 0) {
                if (Hash::check($password, $user->password)) {
                    try {
                        $api_token = sha1($user->id_user . time());
                        $user->update([
                            'api_token' => $api_token
                        ]);

                        return response()->json(
                            [
                                'data' => [
                                    'user' => $user,
                                    'api_token' => $api_token
                                ]
                            ], 200
                        );


                    } catch (\Illuminate\Database\QueryException $ex) {
                        return response()->json(
                            [
                                'status' => '500',
                                'error' => $ex->getMessage(),
                            ], 500
                        );
                    }
                } else {
                    return response()->json(
                        [
                            'status' => '401',
                            'error' => 'Login Failed!',
                        ], 401
                    );
                }
            } else {
                return response()->json(
                    [
                        'status' => '401',
                        'error' => 'User not found',
                    ], 401
                );
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'status' => '500',
                    'error' => $ex->getMessage(),
                ], 500
            );
        }
    }
}