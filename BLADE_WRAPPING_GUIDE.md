# Blade File Wrap-Up Guide
## Practical Examples of Strings Needing __() Wrapping

This document provides line-by-line guidance for wrapping static text strings with `__()` function calls.

---

## File: resources/views/wrongway.blade.php

### Line 60
**BEFORE:**
```blade
<h1 class="text-6xl font-monaco">Wrong Way</h1>
```

**AFTER:**
```blade
<h1 class="text-6xl font-monaco">{{ __('Wrong Way') }}</h1>
```

---

### Line 63
**BEFORE:**
```blade
<p class="text-3xl font-monaco">You shouldn't be here. Please go back.</p>
```

**AFTER:**
```blade
<p class="text-3xl font-monaco">{{ __("You shouldn't be here. Please go back.") }}</p>
```

---

### Line 64
**BEFORE:**
```blade
<a href="{{ route('dashboard') }}" class="font-monaco mt-6 inline-block outline-none text-white px-4 py-2 rounded-lg shadow-md transition-colors">
    Go Back to Dashboard
</a>
```

**AFTER:**
```blade
<a href="{{ route('dashboard') }}" class="font-monaco mt-6 inline-block outline-none text-white px-4 py-2 rounded-lg shadow-md transition-colors">
    {{ __('Go Back to Dashboard') }}
</a>
```

---

## File: resources/views/dashboard.blade.php

### Lines 28-31 (Users Card)
**BEFORE:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users</h5>
    <p class="text-gray-600 dark:text-gray-400">Manage user accounts</p>
    <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">View Users</a>
</div>
```

**AFTER:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Users') }}</h5>
    <p class="text-gray-600 dark:text-gray-400">{{ __('Manage user accounts') }}</p>
    <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">{{ __('View Users') }}</a>
</div>
```

---

### Lines 39-42 (Majors Card)
**BEFORE:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Majors</h5>
    <p class="text-gray-600 dark:text-gray-400">Handle academic majors</p>
    <a href="{{ route('majors.index') }}" class="mt-2 inline-block text-green-600 hover:text-green-800">View Majors</a>
</div>
```

**AFTER:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Majors') }}</h5>
    <p class="text-gray-600 dark:text-gray-400">{{ __('Handle academic majors') }}</p>
    <a href="{{ route('majors.index') }}" class="mt-2 inline-block text-green-600 hover:text-green-800">{{ __('View Majors') }}</a>
</div>
```

---

### Lines 50-53 (Classes Card)
**BEFORE:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Classes</h5>
    <p class="text-gray-600 dark:text-gray-400">Manage class groups</p>
    <a href="{{ route('classes.index') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-800">View Classes</a>
</div>
```

**AFTER:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Classes') }}</h5>
    <p class="text-gray-600 dark:text-gray-400">{{ __('Manage class groups') }}</p>
    <a href="{{ route('classes.index') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-800">{{ __('View Classes') }}</a>
</div>
```

---

## File: resources/views/admin/users/index.blade.php

### Line 16
**BEFORE:**
```blade
<h3 class="text-2xl font-bold">User Management</h3>
```

**AFTER:**
```blade
<h3 class="text-2xl font-bold">{{ __('User Management') }}</h3>
```

---

### Line 18
**BEFORE:**
```blade
<p class="mt-2">Manage all user accounts in the system.</p>
```

**AFTER:**
```blade
<p class="mt-2">{{ __('Manage all user accounts in the system.') }}</p>
```

---

### Line 24
**BEFORE:**
```blade
<h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Users List ({{ $users->total() }})</h4>
```

**AFTER:**
```blade
<h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Users List') }} ({{ $users->total() }})</h4>
```

---

### Line 27 (Add Button)
**BEFORE:**
```blade
<a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    Add User
</a>
```

**AFTER:**
```blade
<a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition-colors">
    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
    </svg>
    {{ __('Add User') }}
</a>
```

---

### Table Headers (Lines 38-56)
**BEFORE:**
```blade
<tr>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Identifier</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
</tr>
```

**AFTER:**
```blade
<tr>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('ID') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Identifier') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Role') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
</tr>
```

---

### Status Badges (Lines 90, 96)
**BEFORE:**
```blade
@if($user->deleted_at)
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
        DELETED
    </span>
@else
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
        ACTIVE
    </span>
@endif
```

**AFTER:**
```blade
@if($user->deleted_at)
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
        {{ __('DELETED') }}
    </span>
@else
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
        {{ __('ACTIVE') }}
    </span>
@endif
```

---

## File: resources/views/auth/login.blade.php

### Lines 5-6 (Login Heading)
**BEFORE:**
```blade
<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back</h1>
<p class="text-gray-600 dark:text-gray-400">Sign in to your account to continue</p>
```

**AFTER:**
```blade
<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Welcome Back') }}</h1>
<p class="text-gray-600 dark:text-gray-400">{{ __('Sign in to your account to continue') }}</p>
```

---

### Line 50 (Remember Me)
**BEFORE:**
```blade
<span class="ms-2 text-sm text-gray-600 dark:text-gray-400 select-none">Remember me</span>
```

**AFTER:**
```blade
<span class="ms-2 text-sm text-gray-600 dark:text-gray-400 select-none">{{ __('Remember me') }}</span>
```

---

### Line 67 (Text Divider)
**BEFORE:**
```blade
<span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">New to Sekai?</span>
```

**AFTER:**
```blade
<span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('New to Sekai?') }}</span>
```

---

### Line 80 (Footer)
**BEFORE:**
```blade
<p>Protected by enterprise-grade security provided by Miku</p>
```

**AFTER:**
```blade
<p>{{ __('Protected by enterprise-grade security provided by Miku') }}</p>
```

---

## File: resources/views/student/student-dashboard.blade.php

### Card Titles and Descriptions
**BEFORE:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">My Classes</h5>
    <p class="text-gray-600 dark:text-gray-400">View enrolled classes</p>
    <a href="{{ route('class.show') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Classes →</a>
</div>
```

**AFTER:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('My Classes') }}</h5>
    <p class="text-gray-600 dark:text-gray-400">{{ __('View enrolled classes') }}</p>
    <a href="{{ route('class.show') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">{{ __('View Classes →') }}</a>
</div>
```

---

## File: resources/views/admin/admin-dashboard.blade.php

### Admin Panel Cards
**BEFORE:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Users</h5>
    <p class="text-gray-600 dark:text-gray-400">Manage user accounts</p>
    <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View Users</a>
</div>
```

**AFTER:**
```blade
<div class="ml-4">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Users') }}</h5>
    <p class="text-gray-600 dark:text-gray-400">{{ __('Manage user accounts') }}</p>
    <a href="{{ route('users.index') }}" class="mt-2 inline-block text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">{{ __('View Users') }}</a>
</div>
```

---

### System Overview Stats
**BEFORE:**
```blade
<div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Users</h5>
    <p class="text-gray-600 dark:text-gray-400 text-2xl">{{ $users }}</p>
</div>
```

**AFTER:**
```blade
<div class="bg-blue-50 dark:bg-blue-900 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
    <h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Total Users') }}</h5>
    <p class="text-gray-600 dark:text-gray-400 text-2xl">{{ $users }}</p>
</div>
```

---

## Common Pattern: Multiple Occurrences Across Files

### Pattern 1: Table Headers (Appears in 10+ files)
**PATTERN:**
```blade
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
    ID/Name/Email/Status/Actions
</th>
```

**SHOULD BECOME:**
```blade
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
    {{ __('ID/Name/Email/Status/Actions') }}
</th>
```

---

### Pattern 2: Card Titles (Appears in 15+ files)
**PATTERN:**
```blade
<h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">
    Card Title Text
</h5>
```

**SHOULD BECOME:**
```blade
<h5 class="text-lg font-medium text-gray-900 dark:text-gray-100">
    {{ __('Card Title Text') }}
</h5>
```

---

### Pattern 3: Button Text (Appears in 20+ files)
**PATTERN:**
```blade
<button type="submit" class="...">Action Text</button>
<a href="{{ route('...') }}" class="...">Link Text</a>
```

**SHOULD BECOME:**
```blade
<button type="submit" class="...">{{ __('Action Text') }}</button>
<a href="{{ route('...') }}" class="...">{{ __('Link Text') }}</a>
```

---

## Implementation Tips

1. **Use Find & Replace:** Many IDEs can do regex-based find & replace to wrap common patterns
2. **Test After Each File:** Verify translations still work
3. **Group Related Strings:** Organize translations by feature (admin.users.*, student.dashboard.*, etc.)
4. **Check Context:** Some strings like "N/A" might be better as a shared translation
5. **Preserve Formatting:** Keep spacing and structure intact when adding `__()` wrappers

---

## Translation File Structure (resources/lang/en.json)

After wrapping, your translation file might look like:

```json
{
  "admin.users.title": "User Management",
  "admin.users.description": "Manage all user accounts in the system.",
  "admin.users.list_title": "Users List",
  "admin.users.add_user": "Add User",
  "admin.dashboard.panel_title": "Admin Panel",
  "admin.dashboard.users_card": "Users",
  "admin.dashboard.users_desc": "Manage user accounts",
  "button.view_users": "View Users",
  "table.column.id": "ID",
  "table.column.name": "Name",
  "table.column.email": "Email",
  "status.active": "ACTIVE",
  "status.deleted": "DELETED"
}
```

