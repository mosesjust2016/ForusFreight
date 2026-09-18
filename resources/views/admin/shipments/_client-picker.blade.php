{{-- Searchable "assign client" picker.
     Expects: $pickerClients (collection of User), $pickerField (string, default user_id),
              $pickerSelected (id or null), $pickerRequired (bool, default true) --}}

@php
    $pickerField   = $pickerField ?? 'user_id';
    $pickerRequired = $pickerRequired ?? true;
    $pickerResolvedId   = old($pickerField, $pickerSelected ?? null);
    $pickerResolvedUser = $pickerClients->firstWhere('id', $pickerResolvedId);
    $pickerLabel        = $pickerResolvedUser
        ? implode(' · ', array_filter([$pickerResolvedUser->name, $pickerResolvedUser->email, $pickerResolvedUser->phone ?? '']))
        : '';
    $pickerData = $pickerClients->map(function ($c) {
        return [
            'id'    => $c->id,
            'name'  => $c->name,
            'email' => (string) $c->email,
            'phone' => (string) ($c->phone ?? ''),
        ];
    })->values();
@endphp

<div class="client-picker" id="clientPicker" data-required="{{ $pickerRequired ? '1' : '0' }}">
    <label>Assign to Client</label>
    <div class="cp-root">
        <input type="text"
               class="cp-input"
               data-cp-input
               placeholder="{{ $pickerLabel ? $pickerResolvedUser->name . ' — type to change' : 'Type a client name, email or phone…' }}"
               value="{{ $pickerLabel }}"
               autocomplete="off">
        <input type="hidden" name="{{ $pickerField }}" data-cp-hidden value="{{ $pickerResolvedId }}">
        <button type="button" class="cp-clear" data-cp-clear title="Clear selection" {{ $pickerResolvedId ? '' : 'hidden' }}>&times;</button>
        <div class="cp-list" data-cp-list></div>
    </div>
    <span class="cp-inline-error" style="display:none;color:#ef4444;font-size:0.75rem;">Please select a client.</span>
    @error($pickerField)<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
</div>

<style>
    .client-picker { margin-bottom: 1rem; }
    .client-picker label {
        display: block; font-weight: 700; font-size: 0.82rem; letter-spacing: 0.4px;
        text-transform: uppercase; color: var(--text-gray, #475569); margin-bottom: 0.5rem;
    }
    .cp-root { position: relative; }
    .cp-input {
        width: 100%; padding: 0.72rem 2.2rem 0.72rem 0.9rem;
        border: 1.5px solid var(--border, #e2e8f0); border-radius: 12px;
        font-size: 0.9rem; color: var(--text-dark, #0f172a); background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .cp-input:focus { outline: none; border-color: var(--primary-green, #4caf50); box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.12); }
    .cp-input.cp-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12); }
    .cp-clear {
        position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%);
        width: 22px; height: 22px; border: none; border-radius: 50%;
        background: #e2e8f0; color: #475569; font-size: 0.8rem; line-height: 1;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .cp-clear:hover { background: #fecaca; color: #b91c1c; }
    .cp-list {
        position: absolute; z-index: 60; top: calc(100% + 0.35rem); left: 0; right: 0;
        background: #fff; border: 1.5px solid var(--border, #e2e8f0); border-radius: 12px;
        box-shadow: 0 12px 30px rgba(2, 6, 23, 0.12); max-height: 260px; overflow-y: auto;
        display: none;
    }
    .cp-list.open { display: block; }
    .cp-opt { display: flex; align-items: center; gap: 0.7rem; padding: 0.55rem 0.8rem; cursor: pointer; }
    .cp-opt:hover, .cp-opt.active { background: #f0fdf4; }
    .cp-opt + .cp-opt { border-top: 1px solid #f1f5f9; }
    .cp-avatar {
        flex: 0 0 34px; width: 34px; height: 34px; border-radius: 50%;
        background: linear-gradient(135deg, #0f766e, #14b8a6); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.78rem; text-transform: uppercase;
    }
    .cp-meta { min-width: 0; }
    .cp-name { font-weight: 600; font-size: 0.88rem; color: var(--text-dark, #0f172a); }
    .cp-sub { font-size: 0.75rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cp-empty { padding: 0.8rem; color: #94a3b8; font-size: 0.82rem; text-align: center; }
</style>

<script>
(function () {
    var root = document.getElementById('clientPicker');
    if (!root) return;

    var data = @json($pickerData);

    var input   = root.querySelector('[data-cp-input]');
    var hidden  = root.querySelector('[data-cp-hidden]');
    var clearBtn = root.querySelector('[data-cp-clear]');
    var list    = root.querySelector('[data-cp-list]');
    var errSpan = root.querySelector('.cp-inline-error');
    var required = root.getAttribute('data-required') === '1';

    function initials(name) {
        return String(name || '?').trim().split(/\s+/).slice(0, 2).map(function (w) { return w[0]; }).join('');
    }

    function labelFor(c) {
        var parts = [c.name];
        if (c.email) parts.push(c.email);
        if (c.phone) parts.push(c.phone);
        return parts.join(' · ');
    }

    function render(filter) {
        var q = (filter || '').toLowerCase().trim();
        var matches = data.filter(function (c) {
            if (!q) return true;
            return (c.name + ' ' + c.email + ' ' + c.phone).toLowerCase().indexOf(q) !== -1;
        });

        list.innerHTML = '';
        if (!matches.length) {
            var empty = document.createElement('div');
            empty.className = 'cp-empty';
            empty.textContent = q ? 'No client matches "' + filter + '"' : 'No clients found';
            list.appendChild(empty);
            return;
        }

        matches.slice(0, 30).forEach(function (c) {
            var opt = document.createElement('div');
            opt.className = 'cp-opt';
            opt.setAttribute('data-id', c.id);

            var av = document.createElement('div');
            av.className = 'cp-avatar';
            av.textContent = initials(c.name);

            var meta = document.createElement('div');
            meta.className = 'cp-meta';
            var name = document.createElement('div');
            name.className = 'cp-name';
            name.textContent = c.name;
            meta.appendChild(name);
            if (c.email || c.phone) {
                var sub = document.createElement('div');
                sub.className = 'cp-sub';
                sub.textContent = [c.email, c.phone].filter(Boolean).join('  ·  ');
                meta.appendChild(sub);
            }

            opt.appendChild(av);
            opt.appendChild(meta);
            opt.addEventListener('mousedown', function (e) { e.preventDefault(); select(c); });
            list.appendChild(opt);
        });

        if (matches.length > 30) {
            var more = document.createElement('div');
            more.className = 'cp-empty';
            more.textContent = matches.length - 30 + ' more match — keep typing…';
            list.appendChild(more);
        }
        list.children.length && (list.children[0].classList.add('active'));
    }

    function openSet(filter) {
        render(filter);
        list.classList.add('open');
    }

    function close() {
        list.classList.remove('open');
    }

    function select(c) {
        hidden.value = c.id;
        input.value = labelFor(c);
        input.placeholder = '';
        clearBtn.hidden = false;
        input.classList.remove('cp-error');
        errSpan.style.display = 'none';
        close();
    }

    function clearSelection() {
        hidden.value = '';
        input.value = '';
        input.placeholder = 'Type a client name, email or phone…';
        clearBtn.hidden = true;
        input.focus();
        openSet('');
    }

    input.addEventListener('focus', function () { openSet(input.value); });
    input.addEventListener('input', function () {
        if (hidden.value && input.value !== labelFor(data.find(function (c) { return c.id == hidden.value; }))) {
            hidden.value = '';
            clearBtn.hidden = true;
        }
        openSet(input.value);
    });

    input.addEventListener('keydown', function (e) {
        var opts = list.querySelectorAll('.cp-opt');
        var activeIdx = Array.prototype.indexOf.call(opts, list.querySelector('.cp-opt.active'));
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!opts.length) return;
            var dir = e.key === 'ArrowDown' ? 1 : -1;
            var next = (activeIdx + dir + opts.length) % opts.length;
            opts.forEach(function (o, i) { o.classList.toggle('active', i === next); });
            opts[next].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            var active = list.querySelector('.cp-opt.active');
            if (active) { e.preventDefault(); var c = data.find(function (x) { return x.id == active.getAttribute('data-id'); }); if (c) select(c); }
        } else if (e.key === 'Escape') {
            close();
        }
    });

    document.addEventListener('mousedown', function (e) {
        if (!root.contains(e.target)) close();
    });

    clearBtn.addEventListener('click', clearSelection);

    root.closest('form').addEventListener('submit', function (e) {
        if (required && !hidden.value) {
            e.preventDefault();
            input.classList.add('cp-error');
            errSpan.style.display = 'block';
            openSet(input.value);
            input.focus();
        }
    });
})();
</script>