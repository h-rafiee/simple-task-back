<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $directories = glob("/opt/myproject/{$user->email}/*", GLOB_ONLYDIR);
        foreach ($directories as &$item) {
            $item = last(explode("/", $item));
        }
        return rest(true, $directories);
    }

    public function make(Request $request)
    {

        $user = $request->user();
        $request->validate([
            'name' => 'required|string'
        ]);

        $path = "/opt/myproject/{$user->email}/{$request->name}";

        if (is_dir($path)) {
            return rest(true, ["message" => "path exist!"]);
        }

        mkdir($path, 0777, true);

        return rest(true, ["message" => "path created!"]);
    }
}
