import os

target_dir = 'resources/views/admin'

for file in os.listdir(target_dir):
    if file.endswith('.blade.php'):
        path = os.path.join(target_dir, file)
        try:
            with open(path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content.replace('Auth::user()', 'optional(Auth::user())')
            if content != new_content:
                with open(path, 'w', encoding='utf-8') as f:
                    f.write(new_content)
        except Exception:
            pass
