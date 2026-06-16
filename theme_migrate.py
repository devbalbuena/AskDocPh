import os
import re

VIEWS_DIR = r"c:\xampp\htdocs\askdocph\resources\views"

# Mapping string/regex replacements
replacements = [
    # Backgrounds and major sections
    (r"bg-gray-900", "bg-gray-50"),
    (r"bg-gray-950", "bg-white"),
    (r"bg-gray-800", "bg-white"),
    (r"bg-gray-700/50", "bg-gray-50"),
    (r"bg-gray-700/30", "bg-gray-50"),
    (r"bg-gray-900/60", "bg-gray-50"),
    (r"bg-gray-900/40", "bg-gray-50"),
    (r"bg-gray-900/30", "bg-gray-50"),
    
    # Text colors
    (r"text-white", "text-gray-900"),
    (r"text-gray-200", "text-gray-700"),
    (r"text-gray-300", "text-gray-700"),
    (r"text-gray-400", "text-gray-500"),
    
    # Borders
    (r"border-gray-800", "border-gray-200"),
    (r"border-gray-700", "border-gray-200"),
    (r"border-gray-600", "border-gray-300"),

    # Purple to Green
    (r"bg-purple-600/30", "bg-green-100"),
    (r"bg-purple-600/20", "bg-green-100"),
    (r"bg-purple-600/10", "bg-green-50"),
    (r"bg-purple-500/20", "bg-green-100"),
    (r"bg-purple-700/40", "bg-green-100"),
    (r"bg-purple-600", "bg-green-600"),
    (r"bg-purple-700", "bg-green-700"),
    (r"text-purple-300", "text-green-700"),
    (r"text-purple-400", "text-green-600"),
    (r"border-purple-700/30", "border-green-200"),
    (r"border-purple-600/50", "border-green-300"),
    (r"border-purple-500/30", "border-green-200"),
    (r"border-purple-500", "border-green-500"),
    (r"focus:border-purple-500", "focus:ring-green-500 focus:border-green-500"),

    # Cards / Panels
    (r"rounded-xl p-5", "rounded-xl shadow-sm border border-gray-100 p-5"),
    (r"rounded-2xl p-6", "rounded-2xl shadow-sm border border-gray-100 p-6"),
    (r"rounded-2xl p-5", "rounded-2xl shadow-sm border border-gray-100 p-5"),
    (r"rounded-xl overflow-hidden", "rounded-xl shadow-sm border border-gray-100 overflow-hidden"),
    
    # Specific Badges overrides explicitly mentioned
    (r"bg-yellow-500/20\s+text-yellow-400", "bg-yellow-100 text-yellow-700"),
    (r"bg-green-500/20\s+text-green-400", "bg-green-100 text-green-700"),
    (r"bg-red-500/20\s+text-red-400", "bg-red-100 text-red-700"),
    (r"bg-blue-500/20\s+text-blue-400", "bg-blue-100 text-blue-700"),
    (r"bg-red-600/20", "bg-red-50"),
    (r"bg-blue-600/30", "bg-blue-100"),
    
    # Fix Blade escaped vars for Bug 2
    (r"@\{\{\s*\$post->user->username\s*\?\?\s*\'\'\s*\}\}", r"{{ $post->user->username ?? '' }}"),

    # Admin chart fix
    (r"rgba\(124,\s*58,\s*237,\s*0\.6\)", r"#16a34a"),
    (r"rgba\(124,\s*58,\s*237,\s*1\)", r"#16a34a"),

    # Inputs placeholder and background fix
    (r"bg-gray-900", "bg-white"),
]

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    new_content = content
    for old, new in replacements:
        # Avoid double replacing shadow-sm
        if "shadow-sm" in new and "shadow-sm" in new_content:
           if new_content.count("shadow-sm border border-gray-100 shadow-sm border border-gray-100") > 0:
               pass
        new_content = re.sub(old, new, new_content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {filepath}")

for root, dirs, files in os.walk(VIEWS_DIR):
    for filename in files:
        if filename.endswith(".blade.php"):
            process_file(os.path.join(root, filename))
