<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebTaskController extends Controller
{
    /**
     * Display the task management interface
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('tasks.index');
    }
    
    /**
     * Show the task dashboard
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('tasks.index');
    }
}
