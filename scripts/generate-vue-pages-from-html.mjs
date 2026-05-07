/**
 * Converts static PHP body (between header/footer includes) → Vue Content + Page wrappers.
 * Run: node scripts/generate-vue-pages-from-html.mjs
 * Skips: index.php, about.php (About.vue exists), insights.php (loops), properties-details.php (loops).
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

import { extractPhpBody, transformMarkup } from './php-markup.mjs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const cmpDir = path.join(root, 'resources', 'js', 'components');
const pageDir = path.join(root, 'resources', 'js', 'pages');

function wrapTemplate(markup) {
  return `<template>\n${markup}\n</template>\n\n<script setup>\nimport { asset } from '@/utils/asset';\n</script>\n`;
}

const SIMPLE_EXPORTS = [
  { php: 'properties.php', content: 'OurPropertyContent.vue', pageComp: 'OurProperty.vue', compExport: 'OurPropertyContent' },
  { php: 'contact.php', content: 'ContactContent.vue', pageComp: 'Contact.vue', compExport: 'ContactContent' },
  { php: 'map.php', content: 'MapContent.vue', pageComp: 'Map.vue', compExport: 'MapContent' },
  { php: 'book-a-viewing.php', content: 'BookAViewingContent.vue', pageComp: 'BookAViewing.vue', compExport: 'BookAViewingContent' },
  { php: 'terms-conditions.php', content: 'TermsConditionsContent.vue', pageComp: 'TermsConditions.vue', compExport: 'TermsConditionsContent' },
  { php: 'thank-you.php', content: 'ThankYouContent.vue', pageComp: 'ThankYou.vue', compExport: 'ThankYouContent' },
  { php: 'career.php', content: 'CareerContent.vue', pageComp: 'Career.vue', compExport: 'CareerContent' },
  { php: 'insights-details.php', content: 'InsightDetailsContent.vue', pageComp: 'InsightDetail.vue', compExport: 'InsightDetailsContent' },
  { php: 'career-details.php', content: 'CareerDetailsContent.vue', pageComp: 'CareerDetail.vue', compExport: 'CareerDetailsContent' },
  { php: '404.php', content: 'NotFoundContent.vue', pageComp: 'NotFound.vue', compExport: 'NotFoundContent' },
];

for (const { php, content, pageComp, compExport } of SIMPLE_EXPORTS) {
  const body = extractPhpBody(php);
  const vue = wrapTemplate(transformMarkup(body));
  fs.mkdirSync(cmpDir, { recursive: true });
  fs.writeFileSync(path.join(cmpDir, content), vue, 'utf8');
  fs.writeFileSync(
    path.join(pageDir, pageComp),
    `<template>\n    <${compExport} />\n</template>\n\n<script setup>\nimport ${compExport} from '@/components/${content}';\n</script>\n`,
    'utf8',
  );
  console.log('generated', php, '→', content, pageComp);
}

console.log('Done.');
