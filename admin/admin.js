const API = '/api';
let editingPostId = null;

function showToast(msg, type) {
    const t = document.getElementById('adminToast');
    t.textContent = msg;
    t.className = `toast ${type}`;
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => t.classList.remove('show'), 3000);
}

function apiHeaders() {
    const token = sessionStorage.getItem('adminToken');
    return { 'Content-Type': 'application/json', ...(token ? { 'Authorization': `Bearer ${token}` } : {}) };
}

async function apiFetch(url, opts = {}) {
    const res = await fetch(url, { ...opts, headers: { ...apiHeaders(), ...opts.headers } });
    if (res.status === 401) { sessionStorage.removeItem('adminToken'); window.location.href = '/'; return null; }
    return res.json();
}

// ── Tab Switching ──
document.querySelectorAll('.admin-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        if (this.dataset.tab === 'dashboard') loadDashboard();
        if (this.dataset.tab === 'posts') loadPosts();
        if (this.dataset.tab === 'feedback') loadFeedback();
        if (this.dataset.tab === 'media') loadMediaGrid();
        if (this.dataset.tab === 'settings') loadSettings();
    });
});

// ── Dashboard ──
async function loadDashboard() {
    const data = await apiFetch(`${API}/admin/storage`);
    if (!data) return;
    const usedGB = (data.totalSize / (1024 * 1024 * 1024)).toFixed(2);
    const freeGB = (data.freeLimit / (1024 * 1024 * 1024)).toFixed(0);
    document.getElementById('storageBar').style.width = data.usagePercent + '%';
    document.getElementById('storageDetails').innerHTML = `<strong>${usedGB} GB</strong> used of ${freeGB} GB free (${data.usagePercent}%)`;
    document.getElementById('storageStatsGrid').innerHTML = `
        <div class="storage-stat"><strong>${data.imageCount}</strong><span>Images</span></div>
        <div class="storage-stat"><strong>${data.videoCount}</strong><span>Videos</span></div>
        <div class="storage-stat"><strong>${data.postCount}</strong><span>Posts</span></div>
        <div class="storage-stat"><strong>${data.bookingCount}</strong><span>Bookings</span></div>
        <div class="storage-stat"><strong>${data.feedbackCount}</strong><span>Feedback</span></div>
        <div class="storage-stat"><strong>${data.totalObjects}</strong><span>Total Files</span></div>
    `;
}

// ── Posts ──
async function loadPosts() {
    const posts = await apiFetch(`${API}/admin/posts`);
    if (!posts) return;
    const container = document.getElementById('postsList');
    if (!posts.length) {
        container.innerHTML = '<p style="color:#a0a0b0;text-align:center;padding:2rem;">No posts yet. Create your first post!</p>';
        return;
    }
    container.innerHTML = posts.map(p => `
        <div class="admin-post-item">
            <div class="post-info">
                <h4>${esc(p.title)} <span class="post-status ${p.published ? 'published' : 'draft'}">${p.published ? 'Published' : 'Draft'}</span></h4>
                <span>${p.event_date || ''} ${p.event_time || ''} ${p.location ? '· ' + esc(p.location) : ''}</span>
            </div>
            <div class="post-actions">
                <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="editPost(${p.id})"><i class="fas fa-edit"></i></button>
                <button class="admin-btn admin-btn-danger admin-btn-sm" onclick="deletePost(${p.id})"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `).join('');
}

function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }

function showPostForm() { document.getElementById('postForm').style.display = 'block'; document.getElementById('postFormTitle').textContent = 'New Post'; editingPostId = null; document.getElementById('postFormElement').reset(); }

function hidePostForm() { document.getElementById('postForm').style.display = 'none'; editingPostId = null; }

async function editPost(id) {
    const posts = await apiFetch(`${API}/admin/posts`);
    if (!posts) return;
    const p = posts.find(x => x.id === id);
    if (!p) return;
    editingPostId = id;
    document.getElementById('postFormTitle').textContent = 'Edit Post';
    document.getElementById('postTitle').value = p.title || '';
    document.getElementById('postCaption').value = p.caption || '';
    document.getElementById('postDate').value = p.event_date || '';
    document.getElementById('postTime').value = p.event_time || '';
    document.getElementById('postLocation').value = p.location || '';
    document.getElementById('postMediaUrl').value = p.media_url || '';
    document.getElementById('postMediaType').value = p.media_type || 'image';
    document.getElementById('postPublished').value = p.published ? '1' : '0';
    document.getElementById('postForm').style.display = 'block';
}

document.getElementById('postFormElement').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {
        title: document.getElementById('postTitle').value,
        caption: document.getElementById('postCaption').value,
        event_date: document.getElementById('postDate').value,
        event_time: document.getElementById('postTime').value,
        location: document.getElementById('postLocation').value,
        media_url: document.getElementById('postMediaUrl').value,
        media_type: document.getElementById('postMediaType').value,
        published: parseInt(document.getElementById('postPublished').value)
    };
    const url = editingPostId ? `${API}/admin/posts/${editingPostId}` : `${API}/admin/posts`;
    const method = editingPostId ? 'PUT' : 'POST';
    const result = await apiFetch(url, { method, body: JSON.stringify(data) });
    if (result) { showToast('Post saved!', 'success'); hidePostForm(); loadPosts(); }
});

async function deletePost(id) {
    if (!confirm('Delete this post?')) return;
    const result = await apiFetch(`${API}/admin/posts/${id}`, { method: 'DELETE' });
    if (result) { showToast('Post deleted', 'success'); loadPosts(); }
}

// ── Media Upload ──
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
uploadArea.addEventListener('click', () => fileInput.click());
uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.style.borderColor = '#4da3ff'; });
uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor = '#2a2a3e'; });
uploadArea.addEventListener('drop', (e) => { e.preventDefault(); uploadArea.style.borderColor = '#2a2a3e'; handleFiles(e.dataTransfer.files); });
fileInput.addEventListener('change', () => handleFiles(fileInput.files));

async function handleFiles(files) {
    const progress = document.getElementById('uploadProgress');
    const fill = document.getElementById('progressFill');
    const status = document.getElementById('uploadStatus');
    progress.style.display = 'block';

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        status.textContent = `Uploading ${file.name} (${i + 1}/${files.length})...`;
        fill.style.width = `${((i) / files.length) * 100}%`;

        const formData = new FormData();
        formData.append('file', file);
        const token = sessionStorage.getItem('adminToken');
        const res = await fetch(`${API}/admin/media/upload`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}` }, body: formData });
        const result = await res.json();
        if (result.success) {
            fill.style.width = `${((i + 1) / files.length) * 100}%`;
            showToast(`${file.name} uploaded`, 'success');
        } else {
            showToast(`Failed: ${file.name}`, 'error');
        }
    }
    status.textContent = 'Complete!';
    setTimeout(() => { progress.style.display = 'none'; fill.style.width = '0'; }, 2000);
    loadMediaGrid();
}

async function loadMediaGrid() {
    const data = await apiFetch(`${API}/admin/storage`);
    if (!data || data.totalObjects === 0) { document.getElementById('mediaGrid').innerHTML = '<p style="color:#a0a0b0;">No media uploaded yet.</p>'; return; }
    // Show recent uploads count
    document.getElementById('mediaGrid').innerHTML = `<p style="color:#a0a0b0;">${data.totalObjects} files (${(data.totalSize / (1024 * 1024)).toFixed(1)} MB). Upload more above.</p>`;
}

// ── Settings ──
async function loadSettings() {
    const settings = await apiFetch(`${API}/admin/settings`);
    if (!settings) return;
    for (const [key, value] of Object.entries(settings)) {
        const el = document.getElementById('setting_' + key);
        if (el) el.value = value;
    }
}

document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = {};
    this.querySelectorAll('input, textarea').forEach(el => {
        const key = el.id.replace('setting_', '');
        data[key] = el.value;
    });
    const result = await apiFetch(`${API}/admin/settings`, { method: 'PUT', body: JSON.stringify(data) });
    if (result) showToast('Settings saved!', 'success');
});

// ── Feedback ──
async function loadFeedback() {
    // Feedback list from main fetch
    const container = document.getElementById('feedbackList');
    container.innerHTML = '<p style="color:#a0a0b0;">Feedback submitted via the site appears here. (Requires separate API endpoint or DB query)</p>';
}

// ── Logout ──
function logout() {
    sessionStorage.removeItem('adminToken');
    showToast('Logged out', 'success');
    setTimeout(() => { window.location.href = '/'; }, 1000);
}

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    if (!sessionStorage.getItem('adminToken')) {
        window.location.href = '/';
        return;
    }
    loadDashboard();
});
