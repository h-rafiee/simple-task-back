<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index()
    {
        $output = [];
        exec("ps aux", $output);
        $cols = [];
        $data = [];
        foreach ($output as $key => $row) {
            $ps = preg_split('/ +/', $row);
            if ($key === 0) {
                $cols = $ps;
            } else {
                if (count($ps) > 11) {
                    $arr = array_slice($ps, 10);
                    $ps[10] = implode(" ", $arr);
                    $ps = array_slice($ps, 0, 11);
                }
                $data[] = array_combine($cols, $ps);
            }
        }
        return rest(true, $data);
    }
}
