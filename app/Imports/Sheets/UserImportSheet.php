<?php

namespace App\Imports\Sheets;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImportSheet implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = new User();
        $user->id = $row['id'];
        $user->name = $row['name'];
        $user->email = $row['email'];
        $user->identifier = $row['identifier'];
        $user->role = $row['role'];
        $user->class_id = $row['class_id'];
        $user->student_order = $row['student_order'];
        // Password is required; we set a placeholder – user should change later.
        $user->password = Hash::make('password123'); // or leave empty? Better to have a default.
        $user->save();
        return $user;
    }
}