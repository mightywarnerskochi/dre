/**
 * Helpers to extract Laravel-style PHP page bodies from html/*.php and fix paths for Vue.
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
export const htmlDir = path.join(path.resolve(__dirname, '..'), 'html');

export function extractPhpBody(fileName) {
  const lines = fs.readFileSync(path.join(htmlDir, fileName), 'utf8').replace(/\r\n/g, '\n').split('\n');
  let start = lines.findIndex((l) => /includes\/header\.php/.test(l) && /require\s+/.test(l));
  if (start === -1) throw new Error(`no header require in ${fileName}`);
  start += 1;
  while (start < lines.length && /^\s*$/.test(lines[start])) start += 1;
  if (/^\?\>\s*$/.test(lines[start] || '')) start += 1;
  while (start < lines.length && /^\s*$/.test(lines[start])) start += 1;
  const endIdx = lines.findIndex((l, i) => i >= start && l.includes('includes/footer.php'));
  if (endIdx === -1) throw new Error(`no footer require in ${fileName}`);
  return lines.slice(start, endIdx).join('\n').trim();
}

export function transformMarkup(html) {
  let h = html.replace(/<\?php[\s\S]*?\?>/g, '');
  const hrefSubs = [
    [/href="properties-details\.php[^"]*"/gi, 'href="/property-details"'],
    [/href="insights-details\.php[^"]*"/gi, 'href="/insights-details"'],
    [/href="career-details\.php[^"]*"/gi, 'href="/career-details"'],
    [/href="book-a-viewing\.php[^"]*"/gi, 'href="/book-a-viewing"'],
    [/href="terms-conditions\.php[^"]*"/gi, 'href="/terms-conditions"'],
    [/href="thank-you\.php[^"]*"/gi, 'href="/thank-you"'],
    [/href="properties\.php[^"]*"/gi, 'href="/our-property"'],
    [/href="index\.php[^"]*"/gi, 'href="/"'],
    [/href="about\.php[^"]*"/gi, 'href="/about"'],
    [/href="insights\.php[^"]*"/gi, 'href="/insights"'],
    [/href="career\.php[^"]*"/gi, 'href="/career"'],
    [/href="contact\.php[^"]*"/gi, 'href="/contact"'],
    [/href="map\.php[^"]*"/gi, 'href="/map"'],
    [/href="404\.php[^"]*"/gi, 'href="/404"'],
  ];
  hrefSubs.forEach(([re, s]) => {
    h = h.replace(re, s);
  });
  h = h.replace(/src="public\/([^"]+)"/g, ":src=\"asset('public/$1')\"");
  return h.trim();
}
