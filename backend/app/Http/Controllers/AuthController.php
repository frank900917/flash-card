<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:8|max:20|unique:users,username',
            'password' => 'required|string|min:8|max:20'
        ], [
            'username.required' => '請輸入帳號',
            'username.min' => '帳號長度至少需 8 個字元',
            'username.max' => '帳號長度不能超過 20 個字元',
            'username.unique' => '此帳號已被註冊，請使用其他帳號',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼長度需至少 8 個字元',
            'password.max' => '密碼長度不能超過 20 個字元',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => '註冊成功！']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:8|max:20',
            'password' => 'required|string|min:8|max:20'
        ], [
            'username.required' => '請輸入帳號',
            'username.min' => '帳號長度至少需 8 個字元',
            'username.max' => '帳號長度不能超過 20 個字元',
            'password.required' => '請輸入密碼',
            'password.min' => '密碼長度需至少 8 個字元',
            'password.max' => '密碼長度不能超過 20 個字元',
        ]);

        if (!Auth::attempt($request->only('username', 'password'))) {
            throw ValidationException::withMessages([
                'username' => ['帳號或密碼錯誤'],
                'password' => ['帳號或密碼錯誤']
            ]);
        }

        $request->session()->regenerate(); // 建立 Session

        return response()->json([
            'message' => '登入成功！',
            'user' => Auth::user()
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate(); // 使 Session 無效
        $request->session()->regenerateToken(); // 重設 CSRF token

        return response()->json([
            'message' => '登出成功！'
        ]);
    }
}
