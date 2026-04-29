import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const aboutPath = path.join(root, 'html', 'about.php');
const outPath = path.join(root, 'resources', 'js', 'pages', 'About.vue');

const lines = fs.readFileSync(aboutPath, 'utf8').split(/\r?\n/);
let html = lines.slice(8, 232).join('\n');

html = html.replace(/src="public\/([^"]+)"/g, ":src=\"asset('public/$1')\"");

html = html.replace(
    `<a href="index.php" aria-label="Home">\n                            <svg`,
    `<RouterLink :to="{ name: 'home' }" aria-label="Home">\n                            <svg`
);
html = html.replace(
    `</svg>\n                        </a>\n                    </li>\n                    <li class="breadcrumb-minimal__sep"`,
    `</svg>\n                        </RouterLink>\n                    </li>\n                    <li class="breadcrumb-minimal__sep"`
);

const file = `<template>\n${html}\n</template>\n\n<script setup>\nimport { RouterLink } from 'vue-router';\nimport { asset } from '@/utils/asset';\n</script>\n`;

fs.mkdirSync(path.dirname(outPath), { recursive: true });
fs.writeFileSync(outPath, file, 'utf8');
console.log('Wrote', outPath);
