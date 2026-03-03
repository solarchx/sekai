# Static Text Mapping for Blade Files
## Strings NOT Wrapped in __() Calls - Candidate for Localization

**Generated:** March 3, 2026

This document contains a structured mapping of all static text strings found in blade files that are NOT currently wrapped in `__()` calls and are eligible for localization.

---

## ROOT LEVEL VIEWS

### [wrongway.blade.php](resources/views/wrongway.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 60 | "Wrong Way" | Title, displayed to user when getting a 403 Forbidden |
| 63 | "You shouldn't be here. Please go back." | Warning message to user when getting a 403 Forbidden |
| 64 | "Go Back to Dashboard" | Button text/link label |

### [dashboard.blade.php](resources/views/dashboard.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 18 | "Admin Panel" | Section header |
| 28 | "Users" | Card title |
| 29 | "Manage user accounts" | Card description |
| 31 | "View Users" | Link text |
| 39 | "Majors" | Card title |
| 40 | "Handle academic majors" | Card description |
| 42 | "View Majors" | Link text |
| 50 | "Classes" | Card title |
| 51 | "Manage class groups" | Card description |
| 53 | "View Classes" | Link text |

### [welcome.blade.php](resources/views/welcome.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 35 | "Dashboard" | Navigation link |
| 37 | "Log in" | Navigation link |
| 40 | "Register" | Navigation link |
| 45 | "Welcome to Sekai" | Main heading |
| 51 | "Documentation" | Card title |
| 55 | "Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end." | Card description |
| 65 | "Laracasts" | Card title |
| 69 | "Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process." | Card description |
| 85 | "Laravel News" | Card title |
| 89 | "Laravel News is a community driven portal and newsletter aggregating all of the latest and most important news in the Laravel ecosystem, including new package releases and tutorials." | Card description |
| 102 | "Vibrant Ecosystem" | Card title |

---

## ADMIN DIRECTORY

### [admin/admin-dashboard.blade.php](resources/views/admin/admin-dashboard.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 14 | "Admin Panel" | Section header |
| 21 | "Users" | Card title |
| 22 | "Manage user accounts" | Card description |
| 23 | "View Users" | Link text |
| 31 | "Majors" | Card title |
| 32 | "Handle academic majors" | Card description |
| 33 | "View Majors" | Link text |
| 41 | "Classes" | Card title |
| 42 | "Manage class groups" | Card description |
| 43 | "View Classes" | Link text |
| 53 | "System Overview" | Section header |
| 54 | "Quick stats about the system." | Section description |
| 59 | "Total Users" | Stat label |
| 64 | "Total Classes" | Stat label |
| 69 | "Total Activities" | Stat label |

### [admin/users/index.blade.php](resources/views/admin/users/index.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 16 | "User Management" | Section header |
| 18 | "Manage all user accounts in the system." | Section description |
| 24 | "Users List" | Table section header |
| 27 | "Add User" | Button text |
| 38 | "ID" | Table column header |
| 41 | "Name" | Table column header |
| 44 | "Email" | Table column header |
| 47 | "Identifier" | Table column header |
| 50 | "Role" | Table column header |
| 53 | "Status" | Table column header |
| 56 | "Actions" | Table column header |
| 90 | "DELETED" | Status badge |
| 96 | "ACTIVE" | Status badge |
| 104 | "Restore this user" | Button title/tooltip |
| 108 | "Edit this user" | Button title/tooltip |
| 114 | "Delete this user" | Button title/tooltip |
| 127 | "Showing ... to ... of ... results" | Pagination info |

### [admin/users/create.blade.php](resources/views/admin/users/create.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 12 | "Add New User" | Section header |
| 13 | "Create a new user account with the required details." | Section description |
| 21 | "Name" | Form label |
| 22 | "e.g., Tanjiro Kamado" | Input placeholder |
| 27 | "Email" | Form label |
| 28 | "e.g., tanjiro@school.edu" | Input placeholder |
| 33 | "Identifier" | Form label |
| 34 | "e.g., TAN001" | Input placeholder |
| 39 | "Role" | Form label |
| 40 | "Student" | Select option |
| 41 | "Teacher" | Select option |
| 42 | "VP" | Select option |
| 43 | "Admin" | Select option |
| 48 | "Password" | Form label |
| 53 | "Confirm Password" | Form label |
| 58 | "Cancel" | Button text |
| 59 | "Create User" | Button text |

### [admin/majors/index.blade.php](resources/views/admin/majors/index.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 16 | "Major Management" | Section header |
| 17 | "Handle academic majors and their details." | Section description |
| 23 | "Majors List" | Table section header |
| 26 | "Add Major" | Button text |
| 37 | "ID" | Table column header |
| 40 | "Name" | Table column header |
| 43 | "Status" | Table column header |
| 46 | "Actions" | Table column header |
| 60 | "DELETED" | Status badge |
| 64 | "ACTIVE" | Status badge |
| 71 | "Restore" | Button text |
| 77 | "Edit" | Button text |
| 84 | "Delete" | Button text |

### [admin/classes/index.blade.php](resources/views/admin/classes/index.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 16 | "Class Management" | Section header |
| 17 | "Manage class groups and their details." | Section description |
| 23 | "Classes List" | Table section header |
| 26 | "Add Class" | Button text |
| 37 | "ID" | Table column header |
| 38 | "Name" | Table column header |
| 39 | "Major" | Table column header |
| 40 | "Grade" | Table column header |
| 41 | "Capacity" | Table column header |
| 42 | "Status" | Table column header |
| 43 | "Actions" | Table column header |
| 58 | "N/A" | Missing data placeholder |
| 68 | "DELETED" | Status badge |
| 72 | "ACTIVE" | Status badge |
| 80 | "Order" | Button text |
| 86 | "Edit" | Button text |
| 93 | "Delete" | Button text |

---

## ACTIVITIES DIRECTORY

### [activities/index.blade.php](resources/views/activities/index.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 16 | "Activity Management" | Section header |
| 17 | "Manage class activities and lessons." | Section description |
| 24 | "Activities List" | Table section header |
| 27 | "Add Activity" | Button text |
| 38 | "ID" | Table column header |
| 41 | "Subject" | Table column header |
| 44 | "Teacher" | Table column header |
| 47 | "Class" | Table column header |
| 50 | "Period" | Table column header |
| 53 | "Status" | Table column header |
| 56 | "Actions" | Table column header |
| 75 | "N/A" | Missing data placeholder |
| 81 | "PERIOD UNAVAILABLE" | Status warning |
| 87 | "DELETED" | Status badge |
| 91 | "ACTIVE" | Status badge |
| 96 | "Restore" | Button text |
| 103 | "Score Dist." | Button text |
| 107 | "Student Scores" | Button text |
| 111 | "Edit" | Button text |
| 117 | "Are you sure?" | Confirmation dialog |
| 122 | "Delete" | Button text |
| 127 | "No activities found" | Empty table message |

### [activities/show.blade.php](resources/views/activities/show.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 16 | "Subject" | Detail label |
| 19 | "Teacher" | Detail label |
| 23 | "Class" | Detail label |
| 26 | "Period" | Detail label |
| 41 | "Edit Activity" | Button text |
| 47 | "Score Distribution" | Button text |
| 53 | "Student Scores" | Button text |
| 60 | "Delete Activity" | Button text |
| 73 | "Activity Forms" | Section header |
| 74 | "View and manage attendance forms for this activity." | Section description |
| 80 | "Forms List" | Table section header |
| 83 | "Add Form" | Button text |
| 95 | "Date" | Table column header |
| 98 | "Activity" | Table column header |
| 101 | "Actions" | Table column header |
| 109 | "View Details" | Link text |
| 111 | "Edit" | Link text |
| 115 | "Are you sure?" | Confirmation dialog |
| 116 | "Delete" | Button text |
| 128 | "Edit Report" | Link text |
| 133 | "Are you sure?" | Confirmation dialog |
| 135 | "Delete Report" | Button text |

---

## AUTH DIRECTORY

### [auth/login.blade.php](resources/views/auth/login.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 5 | "Welcome Back" | Heading |
| 6 | "Sign in to your account to continue" | Subheading |
| 28 | "Email Address" | Form label |
| 29 | "your@email.com" | Input placeholder |
| 39 | "Password" | Form label |
| 40 | "••••••••" | Input placeholder |
| 50 | "Remember me" | Checkbox label |
| 56 | "Sign In" | Button text |
| 67 | "New to Sekai?" | Text divider |
| 73 | "Create Account" | Button text |
| 80 | "Protected by enterprise-grade security provided by Miku" | Footer message |

### [auth/register.blade.php](resources/views/auth/register.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 5 | "Create Account" | Heading |
| 6 | "Join Sekai to get started" | Subheading |
| 63 | "Full Name" | Form label |
| 70 | "Email Address" | Form label |
| 77 | "your@email.com" | Input placeholder |
| 82 | "ID Number" | Form label |
| 89 | "12345678" | Input placeholder |
| 94 | "Password" | Form label |
| 100 | "••••••••" | Input placeholder |
| 105 | "Confirm Password" | Form label |
| 111 | "••••••••" | Input placeholder |
| 118 | "Create Account" | Button text |
| 127 | "Already have an account?" | Text divider |
| 133 | "Sign In" | Button text |
| 139 | "By signing up, you agree to our Terms of Service" | Footer message |

---

## LAYOUTS DIRECTORY

### [layouts/navigation.blade.php](resources/views/layouts/navigation.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 30 | "Manage" | Dropdown trigger (VP/ADMIN) |
| 34 | "Activity Forms" | Dropdown link |
| 35 | "Activity Presences" | Dropdown link |
| 41 | "Teacher" | Dropdown trigger |
| 46 | "VP" | Section header (mobile menu) |

---

## PROFILE DIRECTORY

### [profile/partials/update-profile-information-form.blade.php](resources/views/profile/partials/update-profile-information-form.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 2 | "Profile Information" | Section header |
| 5 | "Update your account's profile information and email address." | Section description |
| 41 | "Your email address is unverified." | Status message |
| 44 | "Click here to re-send the verification email." | Link text |
| 50 | "A new verification link has been sent to your email address." | Success message |
| 55 | "Save" | Button text |
| 61 | "Saved." | Success feedback |

---

## STUDENT DIRECTORY

### [student/student-dashboard.blade.php](resources/views/student/student-dashboard.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 18 | "My Classes" | Card title |
| 19 | "View enrolled classes" | Card description |
| 20 | "View Classes →" | Link text |
| 32 | "My Activities" | Card title |
| 34 | "View and submit attendance for your classes" | Card description |
| 35 | "Go to Activities →" | Link text |
| 47 | "My Grades" | Card title |
| 49 | "View your performance by semester" | Card description |
| 50 | "View Grades →" | Link text |
| 62 | "Announcements" | Card title |
| 63 | "Latest school announcements" | Card description |
| 64 | "View Announcements →" | Link text |

---

## TEACHER DIRECTORY

### [teacher/teacher-dashboard.blade.php](resources/views/teacher/teacher-dashboard.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 17 | "Manage your classes and student progress" | Subtitle |
| 26 | "My Classes" | Card title |
| 27 | "Manage your assigned classes" | Card description |
| 28 | "View Classes →" | Link text |
| 40 | "Mark Attendance" | Card title |
| 41 | "Record student attendance" | Card description |
| 42 | "Mark Attendance →" | Link text |

---

## VP DIRECTORY

### [vp/vp-dashboard.blade.php](resources/views/vp/vp-dashboard.blade.php)
| Line | Static Text | Context |
|------|-------------|---------|
| 17 | "Academic Reports" | Card title |
| 18 | "View performance analytics" | Card description |
| 19 | "View Reports →" | Link text |
| 31 | "Class Overview" | Card title |
| 32 | "Monitor all classes" | Card description |
| 33 | "View Classes →" | Link text |
| 45 | "Teacher Performance" | Card title |
| 46 | "Review teacher evaluations" | Card description |
| 47 | "View Evaluations →" | Link text |
| 59 | "Attendance Summary" | Card title |
| 61 | "School-wide attendance statistics" | Card description |
| 62 | "View Summary →" | Link text |

---

## SUMMARY STATISTICS

- **Total Files Analyzed:** 89 blade files
- **Files with Static Text Found:** ~25 files  
- **Total Static Text Strings NOT in __():** ~150+ strings

## Strings Already Wrapped in __() [COMPLIANT]

The following translations are ALREADY properly wrapped in `__()` calls:
- All main page titles and section headers (Dashboard, Activity Management, etc.)
- All __() wrapped strings in auth files
- All navigation labels using __()
- All button labels in management pages
- Profile update form labels
- Dashboard welcome messages

## Priority for Localization Wrapping

### HIGH PRIORITY (User-facing, frequently visible)
1. Static text in dashboard sections
2. Card titles and descriptions
3. Button labels and link text
4. Form labels and placeholders
5. Status messages and badges
6. Error/confirmation messages

### MEDIUM PRIORITY (Secondary UI elements)
1. Table column headers
2. Section descriptions
3. Placeholder text in lists
4. Tooltip titles

### LOW PRIORITY (Data-dependent, context-specific)
1. Dynamic values that depend on data (IDs, Names from database)
2. Formatted dates and times
3. Conditional status text

---

## Recommendations

1. **Batch Localization:** Group strings by page/section for easier translation management
2. **Translation Keys:** Create consistent key naming (e.g., `admin.users.list_title`, `button.save`)
3. **Context Preservation:** Keep strings in __() context, avoiding string concatenation
4. **RTL Support:** Ensure translations account for RTL language requirements
5. **Pluralization:** Use Laravel's pluralization support for counts (e.g., `{{ trans_choice('item.count', $count) }}`)

