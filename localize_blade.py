#!/usr/bin/env python3
"""
Mass localization script for Laravel Blade templates
Wraps all hardcoded English strings with __() helper
"""

import os
import re
from pathlib import Path

VIEWS_PATH = Path("c:/laragon/www/sekai/resources/views")

# Patterns to NOT wrap
IGNORE_PATTERNS = [
    r'__\(',  # Already wrapped
    r'\{\{',  # Already in expression
    r'@',     # Laravel directive
    r'#',     # Anchor/ID
    r'\$',    # Variable
    r'href=',
    r'placeholder=',
    r'title=',
    r'value=',
    r'onclick=',
    r'class=',
    r'style=',
    r'data-',
]

def should_wrap(text):
    """Check if text should be wrapped"""
    text = text.strip()
    if not text or len(text) < 2:
        return False
    
    # Check if already wrapped or contains PHP/Laravel syntax
    for pattern in IGNORE_PATTERNS:
        if re.search(pattern, text):
            return False
    
    # Check if it's just punctuation or special chars
    if re.match(r'^[^a-zA-Z0-9]+$', text):
        return False
    
    return True

def process_blade_file(filepath):
    """Process a single blade file"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original_content = content
    
    # Pattern 1: Text nodes in tags
    # Example: <h3>My Text</h3>
    def wrap_tag_content(match):
        prefix = match.group(1)
        text = match.group(2)
        suffix = match.group(3)
        
        if should_wrap(text) and '__(' not in text:
            return f"{prefix}{{{{ __('{ text}') }}}}{suffix}"
        return match.group(0)
    
    # Simple approach: find common patterns
    patterns = [
        # Button labels like: >Edit<, >Delete<, etc
        (r'(>)([A-Z][a-zA-Z\s&]+?)(<)', wrap_tag_content),
    ]
    
    for pattern, handler in patterns:
        content = re.sub(pattern, handler, content)
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

def main():
    """Process all blade files"""
    blade_files = list(VIEWS_PATH.rglob("*.blade.php"))
    print(f"Found {len(blade_files)} blade files")
    
    processed = 0
    for filepath in blade_files:
        if process_blade_file(str(filepath)):
            print(f"Updated: {filepath.relative_to(VIEWS_PATH)}")
            processed += 1
    
    print(f"Processed {processed} files")

if __name__ == "__main__":
    main()
