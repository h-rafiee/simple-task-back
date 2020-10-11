<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request) 
    {
        $user = $request->user();
        return rest(true, glob("/opt/myprojcet/{$user->email}/*.txt"));
    }

    public function make(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => 'required|string'
        ]);

        $filePath = "/opt/myproject/{$user->email}/{$request->name}.txt";

        if(file_exists($filePath)) {
            return rest(true, ["message" => "file exist!"]);
        }

        touch($filePath);

        return rest(true, ["message" => "file created!"]);
    }
}
