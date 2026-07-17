from pathlib import Path
root = Path('templates')
link_line = '    <link rel="stylesheet" href="<?= e(BASE_URL . \'/assets/css/responsive.css\') ?>">\n'
count = 0
for path in root.rglob('*.php'):
    text = path.read_text(encoding='utf-8')
    if '<meta name="viewport" content="width=device-width, initial-scale=1.0">' in text and 'responsive.css' not in text:
        new_text = text.replace(
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">\n',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">\n' + link_line,
            1,
        )
        if new_text != text:
            path.write_text(new_text, encoding='utf-8')
            count += 1
print(f'Inserted responsive CSS link in {count} template files.')
