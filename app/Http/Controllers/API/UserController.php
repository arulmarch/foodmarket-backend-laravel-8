<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    use PasswordValidationRules;

    public function login(Request $request)
    {

        try {

            //validasi input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);


            //cek credential (login)
            $credentials = request(['email','password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error(
                    [
                        'message' => 'Unauthorized',
                    ]
                    ,'Authentication Failed', 500
                );
            }

            // jika hash tidak sesuai maka beri error
            $user = User::where('email',$request->email)->first();
            if (!Hash::check($request->password, $user->password,[])) {
                throw new \Exception('Invalid Credentials');
            }

            //jika berhasil maka login
            $tokenResult = $user->createToken('authToken')->plaintTextToken;
            return ResponseFormatter::success([
                'access_token'          => $tokenResult,
                'token_type'            => 'Bearer',
                'user'                  => $user,
            ],'Authenticated');

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Someting when wrong !!',
                'error' => $error,
            ],'Authentication Failed', 500);
        }


    }//function login


    public function register(Request $request)
    {

        try {

            $request->validate([
                'name'      => ['required','string','max:255'],
                'email'     => ['required','string','max:255', 'unique:users'],
                'password'  => $this->passwordRules(),
            ]);


            User::created([
                'name' => $request->name,
                'email' =>  $request->email,
                'address' => $request->address,
                'houseNumber'   => $request->houseNumber,
                'phoneNumber'   => $request->phoneNumber,
                'city'  => $request->city,
                'password'  =>  Hash::make($request->password),
            ]);


            $user = User::where('email',$request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token'  => $tokenResult,
                'token_type'  => 'Bearer',
                'user'  => $user,
            ]);



        } catch (Exception $error) {

            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);

        }

    }//function register


    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }//function logout


    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data profile user berhasil diambil');
    } // ambil data user

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile Updated');
    } // update profile


    public function updatePhoto (Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|image|max:2048'
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(
                                                ['error' => $validator->errors()],
                                                'Update photofails',
                                                401
                                            );
            }


            if ($request->file('file')) {
                $file =  $request->file->store('assets/user','public');

                //simpan foto ke database(ulrnya)
                $user = Auth::user();
                $user->profile_photo_path = $file;
                $user->update();

                return ResponseFormatter::success([$file],'File successfully uploaded');
            }



    } // update photo profile



}
