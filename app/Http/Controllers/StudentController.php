<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SchoolClass;

class StudentController extends Controller
{
    /**
     * 17/02/26
     * may be broken, will fix later 
     * (when i have any idea how to do it /
     * when i have the vision on what i should even fix)
     * - ven
     * 
     * update 19/02/26 - this should work?
     * - ven
     * 
     * update 19/02/26 06:59 - it works but its passing class id
     * instead of user id which is strange but it works so i guess its fine
     * low priority to fix
     * - ven
     * 
     * update 19/02/26 07:17 - fixed, now it passes user id and gets class from there
     * - ven
     */
     public function show(SchoolClass $class)
    {
        // old implementation, passing class id instead of user id
        //  $class->load('major', 'grade');
        //  return view('student.classes.show', compact('class'));

        $class = Auth::user()->class()->with('major', 'grade')->firstOrFail();
        return view('student.classes.show', compact('class'));
    }
}
