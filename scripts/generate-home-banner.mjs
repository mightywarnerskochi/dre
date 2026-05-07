import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const indexPath = path.join(root, 'html', 'index.php');
const outPath = path.join(root, 'resources', 'js', 'components', 'HomeBanner.vue');

const lines = fs.readFileSync(indexPath, 'utf8').split(/\r?\n/);
let html = lines.slice(7, 185).join('\n');

html = html.replace(/src="public\/([^"]+)"/g, ":src=\"asset('public/$1')\"");
html = html.replace(
    /<a href="" class="banner-btn">\s*Explore Rentals\s*<\/a>/,
    '<RouterLink :to="{ name: \'properties\' }" class="banner-btn">Explore Rentals</RouterLink>'
);

const file = `<template>\n${html}\n</template>\n\n<script setup>\nimport { RouterLink } from 'vue-router';\nimport { asset } from '@/utils/asset';\n</script>\n`;

fs.mkdirSync(path.dirname(outPath), { recursive: true });
fs.writeFileSync(outPath, file, 'utf8');
console.log('Wrote', outPath);
