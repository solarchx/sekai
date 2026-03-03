<?php

/**
 * Script to wrap all hardcoded strings in blade views with __() translation helper
 * Run from artisan: php localize_views.php
 */

$viewsPath = __DIR__ . '/resources/views';

$replacements = [
    // Generic UI
    'Create' => 'Create',
    'Update' => 'Update',
    'Edit' => 'Edit',
    'Delete' => 'Delete',
    'Cancel' => 'Cancel',
    'Restore' => 'Restore',
    'Name' => 'Name',
    'Email' => 'Email',
    'Role' => 'Role',
    'ID' => 'ID',
    'Actions' => 'Actions',
    'Status' => 'Status',
    'Class' => 'Class',
    'Subject' => 'Subject',
    'Teacher' => 'Teacher',
    'Student' => 'Student',
    'Semester' => 'Semester',
    'Period' => 'Period',
    'Grade' => 'Grade',
    'Major' => 'Major',
    
    // Messages
    'Are you sure?' => 'Are you sure?',
    'No subjects found' => 'No subjects found',
    'No users found' => 'No users found',
    'No grades found' => 'No grades found',
    'No classes found' => 'No classes found',
    'No majors found' => 'No majors found',
    'No semesters found' => 'No semesters found',
    'No periods found' => 'No periods found',
    'No activities found' => 'No activities found',
    'No records found' => 'No records found',
    
    // Common labels
    'Academic Year' => 'Academic Year',
    'Select Semester' => 'Select Semester',
    'Select a semester' => 'Select a semester',
    'Select an activity' => 'Select an activity',
    'Select a form' => 'Select a form',
];

function processBladeFiles($dir) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        
        if (is_dir($path)) {
            processBladeFiles($path);
        } elseif (substr($file, -10) === '.blade.php') {
            echo "Processing: $path\n";
            processBladeFile($path);
        }
    }
}

function processBladeFile($filePath) {
    $content = file_get_contents($filePath);
    $patterns = [
        // Pattern for text in tags like >Text<
        '/>((?:[^<>]*[^<>\s])[^<>]*)</' => function($matches) {
            $text = trim($matches[1]);
            if (!empty($text) && !preg_match('/^__\(/', $text) && !preg_match('/\{\{/', $text)) {
                // This is a hardcoded string - would need wrapping
                return ">{{ __('$text') }}<";
            }
            return $matches[0];
        }
    ];
    
    // This is complex - better to do manually
    echo "Review needed for: $filePath\n";
}

// Process all views
// processBladeFiles($viewsPath);

echo "Localization script ready. Please wrap strings manually or use find/replace.\n";
