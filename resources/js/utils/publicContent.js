function pickLocalized(text, locale) {
    if (text == null) {
        return '';
    }
    if (typeof text === 'string') {
        return text;
    }
    if (typeof text !== 'object') {
        return '';
    }
    const lang = locale === 'ar' ? 'ar' : 'en';
    const direct = typeof text[lang] === 'string' ? text[lang].trim() : '';
    if (direct !== '') {
        return direct;
    }
    const fallback = typeof text.en === 'string' ? text.en.trim() : '';

    return fallback;
}

function truncateText(text, maxChars) {
    const normalized = typeof text === 'string' ? text.trim() : '';
    if (normalized.length <= maxChars) return normalized;
    return `${normalized.slice(0, maxChars).trimEnd()}...`;
}

export function getPublicContentBoot() {
    if (typeof window === 'undefined') {
        return {};
    }
    const raw = window.__DRE_CONTENT__;
    if (!raw || typeof raw !== 'object') {
        return {};
    }

    return raw;
}

export function getNewsInsightsData(locale) {
    const root = getPublicContentBoot();
    const block = root.newsInsights;
    if (!block || typeof block !== 'object') {
        return { eyebrow: '', title: '', items: [] };
    }
    const section = block.section && typeof block.section === 'object' ? block.section : {};
    const list = Array.isArray(block.items) ? block.items : [];

    return {
        eyebrow: pickLocalized(section.description, locale),
        title: pickLocalized(section.title, locale),
        items: list.map((item) => ({
            id: item.id,
            url: item.url || '#',
            image: item.image || '',
            author: pickLocalized(item.author, locale) || 'DRE Admin',
            publishedAt: item.publishedAt || null,
            title: pickLocalized(item.title, locale),
            excerpt: pickLocalized(item.excerpt, locale),
        })),
    };
}

export function getHomeRentalSectionData(locale) {
    const root = getPublicContentBoot();
    const block = root.rentalSection;
    if (!block || typeof block !== 'object') {
        return { title: '', description: '', displayHome: true, properties: [] };
    }

    const items = Array.isArray(block.properties) ? block.properties : [];

    const normalizeImages = (images) => {
        if (!Array.isArray(images)) return [];
        return images
            .map((img) => {
                if (typeof img === 'string') return img.trim();
                if (img && typeof img === 'object') {
                    const fromImage = typeof img.image === 'string' ? img.image.trim() : '';
                    const fromUrl = typeof img.url === 'string' ? img.url.trim() : '';
                    const fromSrc = typeof img.src === 'string' ? img.src.trim() : '';
                    return fromImage || fromUrl || fromSrc;
                }
                return '';
            })
            .filter((u) => u !== '')
            .slice(0, 3);
    };

    return {
        title: pickLocalized(block.title, locale),
        description: pickLocalized(block.description, locale),
        displayHome: block.displayHome !== false,
        properties: items.map((item) => ({
            id: item.id,
            title: item.title || '',
            location: item.location || '',
            price: Number(item.price || 0),
            period: item.period || '',
            beds: Number(item.beds || 0),
            baths: Number(item.baths || 0),
            sqft: Number(item.sqft || 0),
            images: normalizeImages(item.images),
            url: item.url || null,
            phone: item.phone || '#',
            whatsapp: item.whatsapp || '#',
            inquireUrl: item.inquireUrl || '#',
        })),
    };
}

export function getHomeNeighborhoodsData(locale) {
    const root = getPublicContentBoot();
    const block = root.neighborhoods;
    if (!block || typeof block !== 'object') {
        return {
            title: '',
            description: '',
            items: [],
            endpointBase: '/home/neighborhoods',
            displayHome: true,
        };
    }

    const items = Array.isArray(block.items) ? block.items : [];

    return {
        title: pickLocalized(block.title, locale),
        description: pickLocalized(block.description, locale),
        endpointBase: typeof block.endpointBase === 'string' && block.endpointBase !== ''
            ? block.endpointBase
            : '/home/neighborhoods',
        displayHome: block.displayHome !== false,
        items: items.map((item) => ({
            id: item.id,
            label: pickLocalized(item.label, locale),
            latitude: Number(item.latitude),
            longitude: Number(item.longitude),
        })).filter((item) => Number.isFinite(item.latitude) && Number.isFinite(item.longitude)),
    };
}

export function getInsightsListingData(locale) {
    const root = getPublicContentBoot();
    const block = root.insights;
    if (!block || typeof block !== 'object') {
        return { eyebrow: '', title: '', items: [] };
    }
    const section = block.section && typeof block.section === 'object' ? block.section : {};
    const list = Array.isArray(block.items) ? block.items : [];

    return {
        eyebrow: pickLocalized(section.description, locale),
        title: pickLocalized(section.title, locale),
        items: list.map((item) => ({
            type: pickLocalized(item.type, locale),
            id: item.id,
            slug: item.slug || '',
            url: item.url || '#',
            image: item.image || '',
            author: pickLocalized(item.author, locale) || 'DRE Admin',
            publishedAt: item.publishedAt || null,
            title: pickLocalized(item.title, locale),
            excerpt: pickLocalized(item.excerpt, locale),
            category: pickLocalized(item.category, locale),
        })),
    };
}

export function getInsightDetailData(slug, locale) {
    const listing = getInsightsListingData(locale);
    const items = listing.items;
    if (!items.length) {
        return null;
    }
    const idx = items.findIndex((i) => i.slug === slug);
    const index = idx >= 0 ? idx : 0;

    const root = getPublicContentBoot();
    const rawItems = Array.isArray(root?.insights?.items) ? root.insights.items : [];
    const rawCurrent = rawItems[index] || {};

    const current = {
        ...items[index],
        detailImage: rawCurrent.detailImage || items[index].image || '',
        image3: rawCurrent.image3 || '',
        image4: rawCurrent.image4 || '',
        content: pickLocalized(rawCurrent.content, locale),
        secondDescription: pickLocalized(rawCurrent.secondDescription, locale),
    };

    const prev = index > 0 ? items[index - 1] : null;
    const next = index < items.length - 1 ? items[index + 1] : null;
    const related = items.filter((_, i) => i !== index).slice(0, 4);

    return { current, prev, next, related };
}

export function getHomeAboutData(locale) {
    const root = getPublicContentBoot();
    const block = root.homeAbout;
    if (!block || typeof block !== 'object') {
        return {
            isAvailable: false,
            eyebrow: '',
            title: '',
            body: '',
            readMoreUrl: '/about',
            image: '',
            imageAlt: '',
        };
    }

    return {
        isAvailable: !!block.isAvailable,
        eyebrow: pickLocalized(block.eyebrow, locale),
        title: pickLocalized(block.title, locale),
        body: truncateText(pickLocalized(block.body, locale), locale === 'ar' ? 140 : 170),
        readMoreUrl: block.readMoreUrl || '/about',
        image: block.image || '',
        imageAlt: block.imageAlt || '',
    };
}

export function getAboutPageData(locale) {
    const root = getPublicContentBoot();
    const block = root.aboutPage;
    if (!block || typeof block !== 'object') {
        return {
            hero: { title: '', breadcrumb: '', backgroundImage: '' },
            intro: { eyebrow: '', title: '', bodyHtml: '', gallery: [] },
            missionVision: { items: [] },
            whyChooseUs: { active: false, section: { eyebrow: '', title: '', intro: '', sectionImage: '' }, items: [] },
            journey: { title: '', items: [] },
        };
    }

    return {
        hero: {
            title: pickLocalized(block.hero?.title, locale),
            breadcrumb: pickLocalized(block.hero?.breadcrumb, locale),
            backgroundImage: block.hero?.backgroundImage || '',
        },
        intro: {
            eyebrow: pickLocalized(block.intro?.eyebrow, locale),
            title: pickLocalized(block.intro?.title, locale),
            bodyHtml: pickLocalized(block.intro?.bodyHtml, locale),
            gallery: Array.isArray(block.intro?.gallery) ? block.intro.gallery : [],
        },
        missionVision: {
            title: pickLocalized(block.missionVision?.title, locale),
            items: (Array.isArray(block.missionVision?.items) ? block.missionVision.items : []).map((item) => ({
                index: item.index || '',
                title: pickLocalized(item.title, locale),
                body: pickLocalized(item.body, locale),
                image: item.image || '',
                variant: item.variant || 'plain',
            })),
        },
        whyChooseUs: {
            active: !!block.whyChooseUs?.active,
            section: {
                eyebrow: pickLocalized(block.whyChooseUs?.section?.eyebrow, locale),
                title: pickLocalized(block.whyChooseUs?.section?.title, locale),
                intro: pickLocalized(block.whyChooseUs?.section?.intro, locale),
                sectionImage: block.whyChooseUs?.section?.sectionImage || '',
                sectionImageAlt: block.whyChooseUs?.section?.sectionImageAlt || '',
                collage: Array.isArray(block.whyChooseUs?.section?.collage) ? block.whyChooseUs.section.collage : [],
            },
            items: (Array.isArray(block.whyChooseUs?.items) ? block.whyChooseUs.items : []).map((item) => ({
                index: item.index || '',
                title: pickLocalized(item.title, locale),
                body: pickLocalized(item.body, locale),
            })),
        },
        journey: {
            title: pickLocalized(block.journey?.title, locale),
            items: (Array.isArray(block.journey?.items) ? block.journey.items : []).map((item) => ({
                year: item.year || '',
                bodyHtml: pickLocalized(item.bodyHtml, locale),
                images: Array.isArray(item.images) ? item.images : [],
            })),
        },
    };
}

