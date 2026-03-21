<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spark — UniFi</title>
    <style>
        :root {
            /* UniFi light — clean surfaces, blue accent, sharp corners */
            --unifi-blue: #006fff;
            --unifi-blue-hover: #0056d6;
            --unifi-blue-dim: rgba(0, 111, 255, 0.1);
            --bg-root: #e8ebf0;
            --bg-app: #f0f2f5;
            --bg-sidebar: #ffffff;
            --bg-elevated: #ffffff;
            --bg-input: #fafbfc;
            --bg-hover: #eef1f6;
            --bg-active: #e4e8f0;
            --border-subtle: #dde1e8;
            --border-strong: #c8cdd6;
            --text-primary: #1a1d26;
            --text-secondary: #3d4454;
            --text-muted: #6b7280;
            --danger: #dc2626;
            --danger-hover: #b91c1c;
            --success: #16a34a;
            --radius: 2px;
            --font-sans: "Segoe UI", system-ui, -apple-system, sans-serif;
            --font-mono: "JetBrains Mono", "Consolas", "Liberation Mono", monospace;
            --header-h: 48px;
            --sidebar-w: 260px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            background: var(--bg-root);
            color: var(--text-primary);
            font-family: var(--font-sans);
            font-size: 14px;
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
        }

        .app {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
        }

        /* UniFi-style top bar (light) */
        .topbar {
            height: var(--header-h);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            padding: 0 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f7f8fa 100%);
            border-bottom: 1px solid var(--border-subtle);
            box-shadow: 0 1px 0 rgba(0, 111, 255, 0.06);
        }
        .topbar__brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: -0.02em;
            text-transform: none;
            color: var(--text-primary);
        }
        .topbar__logo {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--text-secondary);
        }
        .topbar__logo svg {
            display: block;
        }
        .topbar__title {
            color: var(--text-primary);
        }
        .topbar__rail {
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-left: 12px;
        }
        .topbar__actions {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .action-group {
            display: flex;
            align-items: center;
            gap: 2px;
            padding: 2px;
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius);
        }
        .action-group__sep {
            width: 1px;
            height: 20px;
            background: var(--border-subtle);
            margin: 0 2px;
        }

        .doc-chip {
            display: inline-flex;
            align-items: center;
            max-width: min(420px, 42vw);
            padding: 6px 12px;
            font-family: var(--font-mono);
            font-size: 12px;
            line-height: 1.35;
            letter-spacing: -0.02em;
            border-radius: var(--radius);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .doc-chip--empty {
            color: var(--text-muted);
            font-weight: 500;
            background: var(--bg-hover);
            border: 1px solid var(--border-subtle);
        }
        /* Open document — console-style, not a loud badge */
        .doc-chip--open {
            font-weight: 500;
            color: var(--text-primary);
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fb 100%);
            border: 1px solid var(--border-strong);
            border-left: 2px solid var(--unifi-blue);
            padding-left: 11px;
            box-shadow:
                0 1px 2px rgba(26, 29, 38, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            border: none;
            border-radius: var(--radius);
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: background 0.12s, color 0.12s, opacity 0.12s;
        }
        .icon-btn:hover:not(:disabled) {
            background: var(--bg-hover);
            color: var(--text-primary);
        }
        .icon-btn:disabled {
            opacity: 0.35;
            cursor: default;
        }
        .icon-btn svg {
            flex-shrink: 0;
        }

        .body {
            flex: 1;
            display: flex;
            min-height: 0;
        }

        /* Sidebar rail (Discord-like structure, light skin) */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .sidebar__head {
            padding: 12px 12px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
        }
        .sidebar__list {
            flex: 1;
            overflow-y: auto;
            padding: 0 8px 12px;
        }
        .sidebar__item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            margin-bottom: 2px;
            border-radius: var(--radius);
            color: var(--text-secondary);
            cursor: pointer;
            border-left: 2px solid transparent;
            user-select: none;
        }
        .sidebar__item:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }
        .sidebar__item.is-active {
            background: var(--bg-active);
            color: var(--text-primary);
            border-left-color: var(--unifi-blue);
        }
        .sidebar__item-icon {
            width: 18px;
            text-align: center;
            opacity: 0.7;
            font-size: 12px;
        }
        .sidebar__item-key {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
        }
        .sidebar__foot {
            padding: 8px;
            border-top: 1px solid var(--border-subtle);
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
            background: var(--bg-app);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background 0.12s, color 0.12s, opacity 0.12s;
        }
        .btn:disabled {
            opacity: 0.45;
            cursor: default;
        }
        .btn--primary {
            background: var(--unifi-blue);
            color: #fff;
        }
        .btn--primary:hover:not(:disabled) {
            background: var(--unifi-blue-hover);
        }
        .btn--secondary {
            background: var(--bg-active);
            color: var(--text-primary);
            border: 1px solid var(--border-strong);
        }
        .btn--secondary:hover:not(:disabled) {
            background: var(--border-subtle);
        }
        .btn--ghost {
            background: transparent;
            color: var(--text-secondary);
        }
        .btn--ghost:hover:not(:disabled) {
            background: var(--bg-hover);
            color: var(--text-primary);
        }
        .btn--danger {
            background: transparent;
            color: var(--danger);
        }
        .btn--danger:hover:not(:disabled) {
            background: rgba(220, 38, 38, 0.1);
        }

        .workspace {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            min-width: 0;
        }

        .pane {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            min-height: 0;
        }

        .pane__label {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background: var(--bg-input);
            border-bottom: 1px solid var(--border-subtle);
        }

        #editor {
            flex: 1;
            width: 100%;
            min-height: 200px;
            padding: 14px 16px;
            margin: 0;
            border: none;
            resize: none;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 1.55;
            color: var(--text-primary);
            background: var(--bg-input);
            outline: none;
        }

        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-align: center;
            padding: 32px;
            background: var(--bg-app);
        }
        /* [hidden] loses to .empty-state { display:flex } without this */
        .empty-state[hidden],
        .workspace[hidden] {
            display: none !important;
        }
        .empty-state h2 {
            margin: 0 0 8px;
            font-size: 16px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Modal overlay — Discord-like */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(26, 29, 38, 0.45);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .modal-backdrop.is-open { display: flex; }
        .modal {
            width: 100%;
            max-width: 420px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
        }
        .modal__head {
            padding: 16px 18px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .modal__body {
            padding: 12px 18px 18px;
            color: var(--text-muted);
            font-size: 14px;
        }
        .modal__foot {
            padding: 0 18px 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .field {
            margin-top: 12px;
        }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 6px;
        }
        .field input {
            width: 100%;
            padding: 10px 12px;
            font-family: var(--font-mono);
            font-size: 14px;
            color: var(--text-primary);
            background: var(--bg-input);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            outline: none;
        }
        .field input:focus {
            border-color: var(--unifi-blue);
            box-shadow: 0 0 0 1px var(--unifi-blue);
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            max-width: 360px;
            padding: 12px 16px;
            background: var(--bg-elevated);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            border-left: 3px solid var(--unifi-blue);
            color: var(--text-primary);
            font-size: 13px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 200;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: opacity 0.2s, transform 0.2s;
        }
        .toast.is-visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }
        .toast--err { border-left-color: var(--danger); }

    </style>
</head>
<body>
    <div class="app">
        <header class="topbar">
            <div class="topbar__brand">
                <span class="topbar__logo" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 10-12h-6l2-8z"/></svg>
                </span>
                <span class="topbar__title">Spark</span>
            </div>
            <div class="topbar__rail">
            <div id="toolbar-label" aria-live="polite">
                <span class="doc-chip doc-chip--empty">No document selected</span>
            </div>
            <div class="topbar__actions" role="toolbar" aria-label="Document actions">
                <div class="action-group">
                    <button type="button" class="icon-btn" id="btn-reload" title="Reload from disk" aria-label="Reload from disk">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 16h5v5"/></svg>
                    </button>
                    <span class="action-group__sep" aria-hidden="true"></span>
                    <button type="button" class="icon-btn" id="btn-save" title="Save (Ctrl+S)" aria-label="Save" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                    </button>
                    <span class="action-group__sep" aria-hidden="true"></span>
                    <button type="button" class="icon-btn" id="btn-delete" title="Delete spark" aria-label="Delete spark" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M10 11v6M14 11v6"/></svg>
                    </button>
                </div>
            </div>
            </div>
        </header>
        <div class="body">
            <aside class="sidebar" aria-label="Sparks list">
                <div class="sidebar__head">Sparks</div>
                <div class="sidebar__list" id="spark-list"></div>
                <div class="sidebar__foot">
                    <button type="button" class="btn btn--secondary" id="btn-new" style="width:100%">+ New spark</button>
                </div>
            </aside>
            <main class="main">
                <div id="workspace-empty" class="empty-state" hidden>
                    <h2>No spark selected</h2>
                    <p>Choose a spark from the list or create a new one.</p>
                </div>
                <div class="workspace" id="workspace">
                    <div class="pane pane--editor">
                        <div class="pane__label">Editor</div>
                        <textarea id="editor" spellcheck="false" placeholder="Write your spark here…&#10;&#10;Plain text (.spk)."></textarea>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal-backdrop" id="modal-new" role="dialog" aria-modal="true" aria-labelledby="modal-new-title">
        <div class="modal">
            <div class="modal__head" id="modal-new-title">New spark</div>
            <div class="modal__body">
                <p>Keys may use letters, numbers, underscores, and hyphens only.</p>
                <div class="field">
                    <label for="new-key-input">Key</label>
                    <input type="text" id="new-key-input" autocomplete="off" placeholder="e.g. product-idea-01">
                </div>
            </div>
            <div class="modal__foot">
                <button type="button" class="btn btn--secondary" id="modal-new-cancel">Cancel</button>
                <button type="button" class="btn btn--primary" id="modal-new-create">Create</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="modal-delete" role="dialog" aria-modal="true" aria-labelledby="modal-delete-title">
        <div class="modal">
            <div class="modal__head" id="modal-delete-title">Delete spark</div>
            <div class="modal__body">
                <p>Delete <strong id="delete-key-name" style="color:var(--text-primary)"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal__foot">
                <button type="button" class="btn btn--secondary" id="modal-delete-cancel">Cancel</button>
                <button type="button" class="btn btn--danger" id="modal-delete-confirm" style="background:var(--danger);color:#fff">Delete</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast" role="status"></div>

    <script>
(function () {
    const API = 'api/index.php';
    const KEY_RE = /^[a-zA-Z0-9_\-]+$/;

    const el = {
        list: document.getElementById('spark-list'),
        editor: document.getElementById('editor'),
        toolbarLabel: document.getElementById('toolbar-label'),
        btnSave: document.getElementById('btn-save'),
        btnDelete: document.getElementById('btn-delete'),
        btnReload: document.getElementById('btn-reload'),
        btnNew: document.getElementById('btn-new'),
        workspace: document.getElementById('workspace'),
        workspaceEmpty: document.getElementById('workspace-empty'),
        modalNew: document.getElementById('modal-new'),
        newKeyInput: document.getElementById('new-key-input'),
        modalNewCancel: document.getElementById('modal-new-cancel'),
        modalNewCreate: document.getElementById('modal-new-create'),
        modalDelete: document.getElementById('modal-delete'),
        deleteKeyName: document.getElementById('delete-key-name'),
        modalDeleteCancel: document.getElementById('modal-delete-cancel'),
        modalDeleteConfirm: document.getElementById('modal-delete-confirm'),
        toast: document.getElementById('toast'),
    };

    let currentKey = null;
    let lastSaved = '';
    let dirty = false;

    function apiUrl(params) {
        const q = new URLSearchParams(params);
        return API + '?' + q.toString();
    }

    function toast(msg, isErr) {
        el.toast.textContent = msg;
        el.toast.classList.toggle('toast--err', !!isErr);
        el.toast.classList.add('is-visible');
        clearTimeout(toast._t);
        toast._t = setTimeout(function () { el.toast.classList.remove('is-visible'); }, 3800);
    }

    function setDirty(v) {
        dirty = v;
        el.btnSave.disabled = !currentKey || !dirty;
    }

    function updateToolbar() {
        if (!currentKey) {
            el.toolbarLabel.innerHTML = '<span class="doc-chip doc-chip--empty">No document selected</span>';
            el.btnDelete.disabled = true;
            el.btnReload.disabled = true;
            el.workspace.hidden = true;
            el.workspaceEmpty.hidden = false;
            return;
        }
        el.toolbarLabel.innerHTML = '<span class="doc-chip doc-chip--open">' + escapeHtml(currentKey) + '</span>';
        el.btnDelete.disabled = false;
        el.btnReload.disabled = false;
        el.workspace.hidden = false;
        el.workspaceEmpty.hidden = true;
    }

    function escapeHtml(s) {
        return s.replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
        });
    }

    function parseHashKey() {
        var raw = window.location.hash.replace(/^#/, '');
        if (!raw) {
            return null;
        }
        try {
            raw = decodeURIComponent(raw);
        } catch (e) {
            return null;
        }
        return KEY_RE.test(raw) ? raw : null;
    }

    function setHashForKey(key) {
        var base = window.location.pathname + window.location.search;
        if (key) {
            history.replaceState(null, '', base + '#' + encodeURIComponent(key));
        } else {
            history.replaceState(null, '', base);
        }
    }

    async function loadKeys() {
        const r = await fetch(API);
        const data = await r.json().catch(function () { return {}; });
        if (!r.ok) {
            toast(data.error || 'Failed to list sparks', true);
            return;
        }
        const keys = data.keys || [];
        el.list.innerHTML = '';
        keys.forEach(function (key) {
            const row = document.createElement('div');
            row.className = 'sidebar__item' + (key === currentKey ? ' is-active' : '');
            row.dataset.key = key;
            row.innerHTML = '<span class="sidebar__item-icon">#</span><span class="sidebar__item-key"></span>';
            row.querySelector('.sidebar__item-key').textContent = key;
            row.addEventListener('click', function () { selectKey(key); });
            el.list.appendChild(row);
        });
    }

    function highlightList() {
        el.list.querySelectorAll('.sidebar__item').forEach(function (node) {
            node.classList.toggle('is-active', node.dataset.key === currentKey);
        });
    }

    async function selectKey(key, opts) {
        opts = opts || {};
        if (dirty && !opts.skipConfirm) {
            if (!confirm('Discard unsaved changes?')) return;
        }
        currentKey = key;
        dirty = false;
        lastSaved = '';
        updateToolbar();
        highlightList();

        if (!key) {
            el.editor.value = '';
            setDirty(false);
            setHashForKey(null);
            return;
        }

        const r = await fetch(apiUrl({ key: key }));
        if (r.status === 404) {
            currentKey = null;
            el.editor.value = '';
            lastSaved = '';
            setDirty(false);
            setHashForKey(null);
            updateToolbar();
            highlightList();
            toast('Spark not found', true);
            await loadKeys();
            return;
        }
        if (!r.ok) {
            const err = await r.json().catch(function () { return {}; });
            toast(err.error || 'Failed to load', true);
            return;
        }
        const text = await r.text();
        el.editor.value = text;
        lastSaved = text;
        setDirty(false);
        setHashForKey(key);
    }

    async function save() {
        if (!currentKey) return;
        const body = el.editor.value;
        const r = await fetch(apiUrl({ key: currentKey }), {
            method: 'PUT',
            headers: { 'Content-Type': 'text/plain; charset=utf-8' },
            body: body,
        });
        const data = await r.json().catch(function () { return {}; });
        if (!r.ok) {
            if (r.status === 404) {
                const c = await fetch(apiUrl({ key: currentKey }), {
                    method: 'POST',
                    headers: { 'Content-Type': 'text/plain; charset=utf-8' },
                    body: body,
                });
                const cdata = await c.json().catch(function () { return {}; });
                if (!c.ok) {
                    toast(cdata.error || 'Save failed', true);
                    return;
                }
            } else {
                toast(data.error || 'Save failed', true);
                return;
            }
        }
        lastSaved = body;
        setDirty(false);
        toast('Saved');
        await loadKeys();
    }

    async function createSpark(key) {
        const r = await fetch(apiUrl({ key: key }), {
            method: 'POST',
            headers: { 'Content-Type': 'text/plain; charset=utf-8' },
            body: '',
        });
        const data = await r.json().catch(function () { return {}; });
        if (!r.ok) {
            toast(data.error || 'Could not create', true);
            return false;
        }
        await loadKeys();
        await selectKey(key, { skipConfirm: true });
        toast('Spark created');
        return true;
    }

    function openModalNew() {
        el.newKeyInput.value = '';
        el.modalNew.classList.add('is-open');
        el.newKeyInput.focus();
    }
    function closeModalNew() {
        el.modalNew.classList.remove('is-open');
    }

    function openModalDelete() {
        if (!currentKey) return;
        el.deleteKeyName.textContent = currentKey;
        el.modalDelete.classList.add('is-open');
    }
    function closeModalDelete() {
        el.modalDelete.classList.remove('is-open');
    }

    async function confirmDelete() {
        if (!currentKey) return;
        const key = currentKey;
        const r = await fetch(apiUrl({ key: key }), { method: 'DELETE' });
        const data = await r.json().catch(function () { return {}; });
        if (!r.ok) {
            toast(data.error || 'Delete failed', true);
            return;
        }
        closeModalDelete();
        currentKey = null;
        el.editor.value = '';
        setDirty(false);
        setHashForKey(null);
        updateToolbar();
        highlightList();
        toast('Deleted');
        await loadKeys();
    }

    el.editor.addEventListener('input', function () {
        setDirty(el.editor.value !== lastSaved);
    });

    el.btnSave.addEventListener('click', save);
    el.btnReload.addEventListener('click', function () {
        if (!currentKey) return;
        if (dirty && !confirm('Reload from disk? Unsaved changes will be lost.')) return;
        selectKey(currentKey, { skipConfirm: true });
    });
    el.btnDelete.addEventListener('click', openModalDelete);
    el.btnNew.addEventListener('click', openModalNew);
    el.modalNewCancel.addEventListener('click', closeModalNew);
    el.modalNew.addEventListener('click', function (e) {
        if (e.target === el.modalNew) closeModalNew();
    });
    el.modalNewCreate.addEventListener('click', async function () {
        const key = el.newKeyInput.value.trim();
        if (!KEY_RE.test(key)) {
            toast('Invalid key: use letters, numbers, _ and - only', true);
            return;
        }
        const ok = await createSpark(key);
        if (ok) closeModalNew();
    });
    el.newKeyInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') el.modalNewCreate.click();
    });

    el.modalDeleteCancel.addEventListener('click', closeModalDelete);
    el.modalDelete.addEventListener('click', function (e) {
        if (e.target === el.modalDelete) closeModalDelete();
    });
    el.modalDeleteConfirm.addEventListener('click', confirmDelete);

    window.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (!el.btnSave.disabled) save();
            return;
        }
        if (e.key === 'Escape') {
            closeModalNew();
            closeModalDelete();
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (!dirty) {
            return;
        }
        e.preventDefault();
        e.returnValue = '';
    });

    window.addEventListener('hashchange', function () {
        var h = parseHashKey();
        if (h === currentKey || (h === null && currentKey === null)) {
            return;
        }
        selectKey(h, {});
    });

    loadKeys().then(function () {
        updateToolbar();
        var h = parseHashKey();
        if (h) {
            return selectKey(h, { skipConfirm: true });
        }
    });
})();
    </script>
</body>
</html>
