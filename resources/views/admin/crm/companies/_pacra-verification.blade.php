<div class="pacra-card" data-pacra-card>
    <div>
        <div class="pacra-eyebrow"><i class="fas fa-shield-check"></i> PACRA verification</div>
        <h2>Check the registered company</h2>
        <p>Search by registered name or company number before saving the CRM record.</p>
    </div>
    <div class="pacra-search">
        <input type="search" data-pacra-search placeholder="e.g. Forus Freight or 120251030444">
        <button type="button" data-pacra-button><i class="fas fa-search"></i> Verify</button>
    </div>
    <div class="pacra-result" data-pacra-result aria-live="polite"></div>
</div>
<style>
    .pacra-card { margin-bottom:1.5rem; padding:1.25rem 1.4rem; border:1px solid rgba(0,127,127,.2); border-radius:16px; background:linear-gradient(135deg,#eef9f7,#fff); }
    .pacra-eyebrow { color:#007f7f; font-size:.7rem; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .pacra-card h2 { margin:.35rem 0; font-size:1rem; color:#0d1b20; }
    .pacra-card p { margin:0 0 1rem; color:#56666c; font-size:.8rem; }
    .pacra-search { display:flex; gap:.6rem; }
    .pacra-search input { flex:1; min-width:0; padding:.7rem .85rem; border:1px solid #d7e5e2; border-radius:10px; }
    .pacra-search button { padding:.7rem 1rem; border:0; border-radius:10px; background:#007f7f; color:white; font-weight:800; cursor:pointer; }
    .pacra-result { margin-top:.8rem; font-size:.8rem; color:#33444a; }
    .pacra-match { display:flex; justify-content:space-between; gap:1rem; align-items:center; padding:.7rem; margin-top:.45rem; background:white; border:1px solid #e2ecea; border-radius:10px; }
    .pacra-match button { border:0; border-radius:8px; padding:.45rem .7rem; background:#ff6200; color:#0d1b20; font-weight:800; cursor:pointer; }
    @media(max-width:520px){.pacra-search{flex-direction:column}.pacra-match{align-items:flex-start;flex-direction:column}}
</style>
<script>
(() => {
    const card = document.querySelector('[data-pacra-card]');
    if (!card) return;
    const input = card.querySelector('[data-pacra-search]');
    const button = card.querySelector('[data-pacra-button]');
    const output = card.querySelector('[data-pacra-result]');
    button.addEventListener('click', async () => {
        if (!input.value.trim()) return;
        button.disabled = true; output.textContent = 'Checking PACRA…';
        try {
            const response = await fetch('{{ route('admin.crm.companies.verify-pacra') }}?search=' + encodeURIComponent(input.value.trim()), { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Verification failed.');
            const rows = Array.isArray(data.results) ? data.results : [];
            output.innerHTML = rows.length ? rows.slice(0, 5).map((row, index) => `<div class="pacra-match"><div><strong>${row.entityName || 'Unnamed company'}</strong><br><span>${row.entityNumber || 'No number'} · ${row.entityStatus || 'Unknown status'} · ${row.natureofBusiness || 'Business activity not listed'}</span></div><button type="button" data-pacra-select="${index}">Use record</button></div>`).join('') : 'No matching PACRA companies found.';
            rows.slice(0, 5).forEach((row, index) => output.querySelector(`[data-pacra-select="${index}"]`)?.addEventListener('click', () => {
                document.querySelector('[name="name"]')?.setAttribute('value', row.entityName || '');
                const industry = document.querySelector('[name="industry"]'); if (industry && row.natureofBusiness) industry.value = row.natureofBusiness;
                const fields = { pacra_entity_number: row.entityNumber, pacra_entity_status: row.entityStatus, pacra_business_nature: row.natureofBusiness };
                Object.entries(fields).forEach(([name, value]) => { let field = document.querySelector(`[name="${name}"]`); if (!field) { field = document.createElement('input'); field.type='hidden'; field.name=name; document.querySelector('form')?.appendChild(field); } field.value = value || ''; });
                output.innerHTML = `<strong style="color:#007f7f">PACRA record selected:</strong> ${row.entityName} (${row.entityNumber || 'number unavailable'})`;
            }));
        } catch (error) { output.textContent = error.message; }
        button.disabled = false;
    });
})();
</script>
