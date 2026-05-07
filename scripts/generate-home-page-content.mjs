import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const indexPath = path.join(root, 'html', 'index.php');
const outPath = path.join(root, 'resources', 'js', 'components', 'HomePageContent.vue');

const lines = fs.readFileSync(indexPath, 'utf8').split(/\r?\n/);
// Banner ends ~line 185; body runs from rental-properties through download-app section (through line 1263)
let html = lines.slice(186, 1263).join('\n');

html = html.replace(/src="public\/([^"]+)"/g, ":src=\"asset('public/$1')\"");

const file = `<template>\n${html}\n</template>\n\n<script setup>\nimport { asset } from '@/utils/asset';\n</script>\n`;

fs.mkdirSync(path.dirname(outPath), { recursive: true });
fs.writeFileSync(outPath, file, 'utf8');
console.log('Wrote', outPath, `(${html.split('\\n').length} chars)`);
