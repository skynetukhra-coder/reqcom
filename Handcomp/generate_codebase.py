import os

# Directories and file extensions to ignore
IGNORE_DIRS = {'.git', '.svn', '__pycache__', 'node_modules', 'vendor', '.idea', 'assets/images'}
IGNORE_EXTS = {'.jpeg', '.jpg', '.png', '.gif', '.pdf', '.zip', '.tar', '.gz', '.mp4', '.mp3', '.exe', '.dll', '.class'}

def get_tree(startpath):
    tree = []
    for root, dirs, files in os.walk(startpath):
        # Modify dirs in-place to prevent os.walk from visiting ignored directories
        dirs[:] = [d for d in dirs if d not in IGNORE_DIRS]
        
        level = root.replace(startpath, '').count(os.sep)
        indent = ' ' * 4 * level
        folder_name = os.path.basename(root)
        if folder_name:
            tree.append(f"{indent}{folder_name}/")
        else:
            tree.append(f"{indent}./")
            
        subindent = ' ' * 4 * (level + 1)
        for f in files:
            if os.path.splitext(f)[1].lower() not in IGNORE_EXTS and f != os.path.basename(__file__):
                tree.append(f"{subindent}{f}")
                
    return '\n'.join(tree)

def generate_codebase_md(startpath, output_filename):
    with open(output_filename, 'w', encoding='utf-8') as out:
        out.write("# Conclusive Project Codebase\n\n")
        
        out.write("## Directory Structure\n\n```text\n")
        out.write(get_tree(startpath))
        out.write("\n```\n\n")
        
        out.write("## File Contents\n\n")

        for root, dirs, files in os.walk(startpath):
            dirs[:] = [d for d in dirs if d not in IGNORE_DIRS]
            
            for file in files:
                ext = os.path.splitext(file)[1].lower()
                # Skip ignored extensions, the script itself, and the output file
                if ext in IGNORE_EXTS or file == os.path.basename(output_filename) or file == os.path.basename(__file__):
                    continue

                file_path = os.path.join(root, file)
                rel_path = os.path.relpath(file_path, startpath)
                
                try:
                    with open(file_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    out.write(f"### `{rel_path}`\n\n")
                    lang = ext.replace('.', '') if ext else 'text'
                    out.write(f"```{lang}\n{content}\n```\n\n")
                except Exception as e:
                    print(f"Skipping {rel_path}: Could not read file ({e})")

if __name__ == "__main__":
    root_dir = os.path.dirname(os.path.abspath(__file__))
    output_file = os.path.join(root_dir, "code_base.md")
    generate_codebase_md(root_dir, output_file)
    print(f"✅ Success! Codebase documentation generated at: {output_file}")