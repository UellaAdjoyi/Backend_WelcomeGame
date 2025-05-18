<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index() {
        return Mission::all();
    }

    public function store(Request $request) {
        $request->validate(['title' => 'required', 'description' => 'required']);
        return Mission::create($request->all());
    }}
