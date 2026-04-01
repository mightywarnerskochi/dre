/**
 * Property Listing and Detail — Frontend Logic (Axios).
 */

document.addEventListener('DOMContentLoaded', () => {
    const listingEl = document.getElementById('dre-properties-listing');
    const detailEl = document.getElementById('dre-property-detail');

    if (listingEl) initListing(listingEl);
    if (detailEl) initDetail(detailEl);
});

/**
 * Initialize Listing Page
 */
async function initListing(container) {
    const filters = {
        location: '',
        type: '',
        beds: '',
        baths: '',
        price_min: '',
        price_max: '',
        page: 1
    };

    const grid = container.querySelector('.dre-property-grid');
    const pagination = container.querySelector('.dre-pagination');
    const filterForm = document.getElementById('dre-filter-form');

    const fetchProperties = async () => {
        try {
            grid.innerHTML = '<div class="dre-loading-overlay">Loading...</div>';
            const params = new URLSearchParams(filters);
            const response = await axios.get(`/api/properties?${params.toString()}`);
            renderProperties(response.data);
            renderPagination(response.data);
        } catch (error) {
            console.error('Error fetching properties:', error);
            grid.innerHTML = '<div class="dre-error">Failed to load properties.</div>';
        }
    };

    const renderProperties = (data) => {
        if (!data.data.length) {
            grid.innerHTML = '<div class="dre-no-results">No properties found matching your criteria.</div>';
            return;
        }

        grid.innerHTML = data.data.map(p => `
            <div class="dre-property-card">
                <div class="dre-card-image">
                    ${p.featured_image ? `<img src="${p.featured_image}" alt="${p.title}">` : ''}
                    <div class="dre-featured-badge">Featured</div>
                </div>
                <div class="dre-card-body">
                    <h3 class="dre-card-title"><a href="/properties/${p.slug}">${p.title}</a></h3>
                    <p class="dre-card-location">${p.location}</p>
                    <div class="dre-card-price">${new Intl.NumberFormat().format(p.price)} ${p.currency}</div>
                    <div class="dre-card-specs">
                        <div class="dre-spec-item"><span>${p.bedrooms}</span> Beds</div>
                        <div class="dre-spec-item"><span>${p.bathrooms}</span> Baths</div>
                        <div class="dre-spec-item"><span>${p.sqft}</span> ft²</div>
                    </div>
                </div>
            </div>
        `).join('');
    };

    const renderPagination = (data) => {
        if (data.last_page <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let html = '';
        for (let i = 1; i <= data.last_page; i++) {
            html += `<button class="dre-page-btn ${i === data.current_page ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }
        pagination.innerHTML = html;

        pagination.querySelectorAll('.dre-page-btn').forEach(btn => {
            btn.onclick = () => {
                filters.page = btn.dataset.page;
                fetchProperties();
            };
        });
    };

    // Filter Listeners
    if (filterForm) {
        filterForm.addEventListener('change', (e) => {
            filters[e.target.name] = e.target.value;
            filters.page = 1;
            fetchProperties();
        });

        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchProperties();
        });
    }

    fetchProperties();
}

/**
 * Initialize Detail Page
 */
async function initDetail(container) {
    const propertyId = container.dataset.propertyId;
    
    try {
        const response = await axios.get(`/api/properties/${propertyId}`);
        renderDetail(response.data.property);
        renderSimilar(response.data.similar);
        initMortgageCalculator();
    } catch (error) {
        console.error('Error fetching property detail:', error);
        container.innerHTML = '<div class="dre-error">Failed to load property details.</div>';
    }

    function renderDetail(p) {
        if (!p) return;

        // Gallery
        const gallery = container.querySelector('.dre-detail-gallery');
        if (p.images && p.images.length > 0) {
            gallery.innerHTML = `
                <div class="dre-main-img"><img src="${p.images[0]}" alt="${p.title}"></div>
                <div class="dre-side-imgs">
                    <div class="dre-side-img"><img src="${p.images[1] || p.images[0]}" alt="Secondary"></div>
                    <div class="dre-side-img"><img src="${p.images[2] || p.images[0]}" alt="Tertiary"></div>
                </div>
            `;
        }

        // Main Content
        container.querySelector('.dre-description').innerHTML = p.description || 'No description available.';

        // Details Grid
        const grid = container.querySelector('.dre-detail-grid');
        grid.innerHTML = (p.details_grid || []).map(d => `
            <div class="dre-grid-item">
                <span class="dre-grid-label">${d.label || 'Detail'}</span>
                <span class="dre-grid-value">${d.value || 'N/A'}</span>
            </div>
        `).join('');

        // Amenities
        const amenities = container.querySelector('.dre-amenities-list');
        amenities.innerHTML = (p.amenities || []).map(a => `
            <div class="dre-amenity-item">${a}</div>
        `).join('');

        // Features
        const features = container.querySelector('.dre-features-list');
        features.innerHTML = (p.features || []).map(f => `
            <li>${f}</li>
        `).join('');

        // Nearby Places (Easy Access)
        const nearby = container.querySelector('.dre-nearby-list');
        nearby.innerHTML = (p.easy_access || []).map(n => `
            <div class="dre-amenity-item">
                ${n.icon ? `<img src="${n.icon}" width="20" height="20" alt="${n.name}">` : ''}
                <span>${n.name} - ${n.distance} km</span>
            </div>
        `).join('');

        // Agent Info
        const agentInfo = container.querySelector('.dre-agent-info');
        if (p.agent) {
            agentInfo.innerHTML = `
                <div class="dre-agent-profile">
                    <img src="${p.agent.image}" alt="${p.agent.name}" style="width: 60px; height: 60px; border-radius: 50%;">
                    <div>
                        <strong>${p.agent.name}</strong>
                        <p>${p.agent.designation || 'Leasing Executive'}</p>
                    </div>
                </div>
                <div class="dre-agent-actions">
                    <a href="tel:${p.agent.phone}" class="dre-search-btn" style="display: block; text-align: center; margin: 10px 0;">Call Now</a>
                    <a href="https://wa.me/${p.agent.whatsapp}" class="dre-search-btn" style="display: block; text-align: center; background: #25d366;">WhatsApp</a>
                </div>
            `;
        }
    }

    function renderSimilar(similar) {
        const similarGrid = document.getElementById('similar-listings-grid');
        if (!similarGrid || !similar.length) return;

        similarGrid.innerHTML = similar.map(p => `
            <div class="dre-property-card">
                 <div class="dre-card-image">
                    <img src="${p.featured_image}" alt="${p.title}">
                </div>
                <div class="dre-card-body">
                    <h4 class="dre-card-title"><a href="/properties/${p.slug}">${p.title}</a></h4>
                    <p class="dre-card-location">${p.location}</p>
                    <div class="dre-card-price">${new Intl.NumberFormat().format(p.price)} ${p.currency}</div>
                </div>
            </div>
        `).join('');
    }

    function initMortgageCalculator() {
        const calc = document.getElementById('mortgage-calculator');
        if (!calc) return;

        const inputs = calc.querySelectorAll('input');
        const result = calc.querySelector('.calc-result');

        const calculate = () => {
            const price = parseFloat(calc.querySelector('[name="price"]').value) || 0;
            const downPayment = parseFloat(calc.querySelector('[name="downpayment"]').value) || 0;
            const rate = parseFloat(calc.querySelector('[name="rate"]').value) / 100 / 12 || 0;
            const years = parseFloat(calc.querySelector('[name="years"]').value) * 12 || 1;

            const principal = price - downPayment;
            if (principal <= 0) {
                result.textContent = '0 AED';
                return;
            }

            const monthly = (principal * rate * Math.pow(1 + rate, years)) / (Math.pow(1 + rate, years) - 1);
            result.textContent = isFinite(monthly) ? `${new Intl.NumberFormat().format(Math.round(monthly))} AED` : '0 AED';
        };

        inputs.forEach(i => i.oninput = calculate);
        calculate();
    }
}
