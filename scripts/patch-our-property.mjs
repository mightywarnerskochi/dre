import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const p = path.join(__dirname, '../resources/js/components/OurPropertyContent.vue');
let t = fs.readFileSync(p, 'utf8');
const start = t.indexOf('            <div class=" properties-grid');
const end = t.indexOf('            </div>\n        </div>\n    </section>\n\n\n\n</template>');
if (start === -1 || end === -1) throw new Error('markers not found');

const newBlock = '            <OurPropertyListingGrid />\n';
t = t.slice(0, start) + newBlock + t.slice(end);
fs.writeFileSync(p, t);
console.log('patched', p);
