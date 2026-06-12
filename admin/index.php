<?php
require_once __DIR__ . '/../includes/config.php';

$isLoggedIn = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AYM Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: #0d0d1a;
  color: #e8e8f0;
  min-height: 100vh;
}
a { color: #4da3ff; text-decoration: none; }
a:hover { text-decoration: underline; }

/* ─── Login ─── */
.login-page {
  display: flex; align-items: center; justify-content: center;
  min-height: 100vh; padding: 2rem;
}
.login-card {
  background: #1a1a2e; border-radius: 16px;
  padding: 2.5rem; width: 100%; max-width: 420px;
  border: 1px solid #2a2a3e;
}
.login-card h1 {
  text-align: center; margin-bottom: 0.25rem;
  font-size: 1.5rem; color: #fff;
}
.login-card p {
  text-align: center; color: #9494a8;
  font-size: 0.85rem; margin-bottom: 1.5rem;
}
.login-card label {
  display: block; font-size: 0.8rem; font-weight: 600;
  color: #9494a8; margin-bottom: 0.35rem;
}
.login-card input {
  width: 100%; padding: 0.7rem 0.9rem;
  background: #0d0d1a; border: 1px solid #2a2a3e;
  border-radius: 8px; color: #e8e8f0; font-size: 0.9rem;
  margin-bottom: 1rem; outline: none;
}
.login-card input:focus { border-color: #4da3ff; }
.login-card button {
  width: 100%; padding: 0.75rem;
  background: #4da3ff; color: #fff; border: none;
  border-radius: 8px; font-size: 0.95rem; font-weight: 600;
  cursor: pointer; transition: background 0.3s;
}
.login-card button:hover { background: #1a6bff; }
.login-error {
  background: rgba(239,68,68,0.15); border: 1px solid #ef4444;
  color: #fca5a5; padding: 0.6rem 0.9rem; border-radius: 8px;
  font-size: 0.8rem; margin-bottom: 1rem; display: none;
}

/* ─── Admin Layout ─── */
.admin-container { display: none; max-width: 1200px; margin: 0 auto; padding: 1.5rem; }
.admin-container.active { display: block; }
.admin-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.5rem; padding-bottom: 1rem;
  border-bottom: 1px solid #2a2a3e;
}
.admin-header h1 { font-size: 1.3rem; color: #fff; }
.admin-header h1 i { color: #4da3ff; margin-right: 0.5rem; }
.admin-header-actions { display: flex; gap: 0.75rem; align-items: center; }
.admin-header-actions a, .admin-header-actions button {
  font-size: 0.8rem; padding: 0.4rem 0.8rem;
  border-radius: 6px; cursor: pointer;
}
.btn-view-site { background: #1e1e30; color: #e8e8f0; border: 1px solid #2a2a3e; }
.btn-logout { background: #dc2626; color: #fff; border: none; }
.btn-logout:hover { background: #b91c1c; }

/* ─── Tabs ─── */
.tabs {
  display: flex; gap: 0.25rem; margin-bottom: 1.5rem;
  background: #1a1a2e; border-radius: 12px; padding: 0.35rem;
  overflow-x: auto;
}
.tab-btn {
  flex: 1; padding: 0.6rem 1rem; border: none; border-radius: 8px;
  background: transparent; color: #9494a8; font-size: 0.8rem;
  font-weight: 600; cursor: pointer; transition: all 0.3s;
  white-space: nowrap;
}
.tab-btn i { margin-right: 0.35rem; }
.tab-btn:hover { color: #e8e8f0; }
.tab-btn.active { background: #0d0d1a; color: #4da3ff; }
.tab-content { display: none; }
.tab-content.active { display: block; }

/* ─── Cards ─── */
.card {
  background: #1a1a2e; border-radius: 12px;
  padding: 1.5rem; margin-bottom: 1rem;
  border: 1px solid #2a2a3e;
}
.card h3 { font-size: 1rem; margin-bottom: 0.75rem; color: #fff; }

/* ─── Stats Grid ─── */
.stats-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;
}
.stat-item {
  background: #0d0d1a; border-radius: 10px;
  padding: 1rem; text-align: center;
  border: 1px solid #2a2a3e;
}
.stat-item .num { font-size: 1.6rem; font-weight: 700; color: #4da3ff; display: block; }
.stat-item .label { font-size: 0.72rem; color: #9494a8; margin-top: 0.2rem; }

/* ─── Storage Bar ─── */
.storage-bar-wrap { margin-top: 1rem; }
.storage-bar {
  height: 8px; background: #0d0d1a; border-radius: 4px; overflow: hidden; margin-bottom: 0.4rem;
}
.storage-bar-fill {
  height: 100%; background: linear-gradient(90deg, #4da3ff, #22c55e);
  border-radius: 4px; transition: width 0.5s ease;
}
.storage-info { display: flex; justify-content: space-between; font-size: 0.75rem; color: #9494a8; }

/* ─── Upload Area ─── */
.upload-zone {
  border: 2px dashed #2a2a3e; border-radius: 12px;
  padding: 2.5rem; text-align: center; cursor: pointer;
  transition: border-color 0.3s, background 0.3s;
}
.upload-zone:hover, .upload-zone.dragover {
  border-color: #4da3ff; background: rgba(77,163,255,0.05);
}
.upload-zone i { font-size: 2.5rem; color: #4da3ff; margin-bottom: 0.75rem; }
.upload-zone p { font-size: 0.85rem; color: #9494a8; }
.upload-zone input { display: none; }
.progress-bar-wrap { margin-top: 0.75rem; display: none; }
.progress-bar-wrap.show { display: block; }
.progress-bar { height: 6px; background: #0d0d1a; border-radius: 4px; overflow: hidden; }
.progress-fill { height: 100%; background: #4da3ff; width: 0; border-radius: 4px; transition: width 0.3s; }
.progress-text { font-size: 0.75rem; color: #9494a8; margin-top: 0.3rem; }

/* ─── Posts Table ─── */
.posts-toolbar { display: flex; justify-content: space-between; margin-bottom: 1rem; }
.btn-primary { background: #4da3ff; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
.btn-primary:hover { background: #1a6bff; }
.btn-danger { background: #dc2626; color: #fff; border: none; padding: 0.4rem 0.7rem; border-radius: 6px; cursor: pointer; font-size: 0.75rem; }
.btn-danger:hover { background: #b91c1c; }
.btn-edit { background: transparent; color: #4da3ff; border: 1px solid #4da3ff; padding: 0.4rem 0.7rem; border-radius: 6px; cursor: pointer; font-size: 0.75rem; }
.btn-edit:hover { background: rgba(77,163,255,0.1); }

.post-item {
  display: flex; justify-content: space-between; align-items: center;
  padding: 0.75rem 0; border-bottom: 1px solid #2a2a3e;
}
.post-item:last-child { border-bottom: none; }
.post-item-info h4 { font-size: 0.9rem; color: #fff; margin-bottom: 0.15rem; }
.post-item-info span { font-size: 0.72rem; color: #9494a8; }
.post-item-actions { display: flex; gap: 0.5rem; }
.badge { font-size: 0.65rem; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600; }
.badge-published { background: rgba(34,197,94,0.15); color: #22c55e; }
.badge-draft { background: rgba(251,191,36,0.15); color: #fbbf24; }
.empty-state { text-align: center; padding: 2rem; color: #9494a8; font-size: 0.85rem; }
.empty-state i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }

/* ─── Form ─── */
.form-overlay {
  display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  z-index: 100; align-items: center; justify-content: center;
  padding: 1rem;
}
.form-overlay.show { display: flex; }
.form-modal {
  background: #1a1a2e; border-radius: 16px; padding: 2rem;
  width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto;
  border: 1px solid #2a2a3e;
}
.form-modal h2 { font-size: 1.1rem; margin-bottom: 1rem; color: #fff; }
.form-modal label { display: block; font-size: 0.78rem; font-weight: 600; color: #9494a8; margin-bottom: 0.25rem; }
.form-modal input, .form-modal textarea, .form-modal select {
  width: 100%; padding: 0.55rem 0.75rem;
  background: #0d0d1a; border: 1px solid #2a2a3e;
  border-radius: 8px; color: #e8e8f0; font-size: 0.85rem;
  margin-bottom: 0.75rem; outline: none;
}
.form-modal input:focus, .form-modal textarea:focus, .form-modal select:focus { border-color: #4da3ff; }
.form-modal textarea { min-height: 80px; resize: vertical; }
.form-actions { display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem; }
.btn-cancel { background: transparent; color: #9494a8; border: 1px solid #2a2a3e; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; }
.btn-cancel:hover { color: #e8e8f0; }

/* ─── Media Grid ─── */
.media-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 0.5rem; margin-top: 1rem;
}
.media-thumb {
  aspect-ratio: 1; border-radius: 8px; overflow: hidden;
  background: #0d0d1a; border: 1px solid #2a2a3e;
  position: relative;
}
.media-thumb img, .media-thumb video {
  width: 100%; height: 100%; object-fit: cover;
}
.media-thumb .file-size {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: rgba(0,0,0,0.7); font-size: 0.6rem;
  padding: 0.2rem 0.35rem; color: #ccc;
  text-align: center;
}

/* ─── Settings ─── */
.settings-grid { display: grid; gap: 0.75rem; }
.setting-row { display: flex; align-items: center; gap: 0.75rem; }
.setting-row label { min-width: 140px; font-size: 0.8rem; color: #9494a8; font-weight: 600; }
.setting-row input, .setting-row textarea {
  flex: 1; padding: 0.5rem 0.7rem;
  background: #0d0d1a; border: 1px solid #2a2a3e;
  border-radius: 6px; color: #e8e8f0; font-size: 0.85rem; outline: none;
}
.setting-row input:focus, .setting-row textarea:focus { border-color: #4da3ff; }

/* ─── Feedback List ─── */
.feedback-item {
  padding: 0.75rem 0; border-bottom: 1px solid #2a2a3e;
}
.feedback-item:last-child { border-bottom: none; }
.feedback-item h4 { font-size: 0.85rem; color: #fff; }
.feedback-item .meta { font-size: 0.7rem; color: #6b6b80; margin: 0.15rem 0 0.35rem; }
.feedback-item p { font-size: 0.8rem; color: #c8c8d0; line-height: 1.5; }
.stars { color: #fbbf24; font-size: 0.75rem; }

/* ─── Toast ─── */
.toast {
  position: fixed; bottom: 1.5rem; right: 1.5rem;
  padding: 0.75rem 1.25rem; border-radius: 10px;
  font-size: 0.85rem; font-weight: 500; z-index: 200;
  transform: translateY(120%); transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
  max-width: 360px;
}
.toast.show { transform: translateY(0); }
.toast.success { background: #065f46; border-left: 4px solid #22c55e; color: #d1fae5; }
.toast.error { background: #7f1d1d; border-left: 4px solid #ef4444; color: #fecaca; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .admin-container { padding: 1rem; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .tabs { gap: 0; }
  .tab-btn { font-size: 0.72rem; padding: 0.5rem 0.6rem; }
  .tab-btn i { margin-right: 0.2rem; }
  .admin-header { flex-direction: column; gap: 0.75rem; text-align: center; }
  .setting-row { flex-direction: column; align-items: stretch; }
  .setting-row label { min-width: auto; }
  .form-modal { padding: 1.5rem; }
}
@media (max-width: 480px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .login-card { padding: 1.5rem; }
}
</style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
<div class="login-page" id="loginPage">
  <div class="login-card">
    <h1><i class="fas fa-shield-alt"></i> AYM Admin</h1>
    <p>Enter your secret credentials</p>
    <div class="login-error" id="loginError">Invalid credentials</div>
    <form id="loginForm">
      <label>Name</label>
      <input type="text" name="name" required>
      <label>Surname</label>
      <input type="text" name="surname" required>
      <label>Phrase</label>
      <input type="text" name="phrase" required>
      <label>Rating</label>
      <input type="number" name="rating" required>
      <button type="submit"><i class="fas fa-lock"></i> Sign In</button>
    </form>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = new FormData(this);
  const errEl = document.getElementById('loginError');
  errEl.style.display = 'none';

  try {
    const res = await fetch('/api/admin/login.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.admin) {
      location.reload();
    } else {
      errEl.textContent = data.error || 'Invalid credentials';
      errEl.style.display = 'block';
    }
  } catch {
    errEl.textContent = 'Connection error';
    errEl.style.display = 'block';
  }
});
</script>
<?php endif; ?>

<div class="admin-container <?php echo $isLoggedIn ? 'active' : ''; ?>" id="adminContainer">
  <div class="admin-header">
    <h1><i class="fas fa-church"></i> AYM Admin</h1>
    <div class="admin-header-actions">
      <a href="/" target="_blank" class="btn-view-site"><i class="fas fa-external-link-alt"></i> View Site</a>
      <button class="btn-logout" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </div>

  <div class="tabs">
    <button class="tab-btn active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</button>
    <button class="tab-btn" data-tab="posts"><i class="fas fa-newspaper"></i> Posts</button>
    <button class="tab-btn" data-tab="media"><i class="fas fa-images"></i> Media</button>
    <button class="tab-btn" data-tab="settings"><i class="fas fa-cog"></i> Settings</button>
    <button class="tab-btn" data-tab="feedback"><i class="fas fa-comments"></i> Feedback</button>
  </div>

  <div class="tab-content active" id="tab-dashboard">
    <div class="card">
      <h3><i class="fas fa-database"></i> Storage</h3>
      <div class="storage-bar-wrap">
        <div class="storage-bar"><div class="storage-bar-fill" id="storageBar" style="width:0%"></div></div>
        <div class="storage-info"><span id="storageUsed">0 MB</span><span id="storageMax">10 GB</span></div>
      </div>
    </div>
    <div class="stats-grid" id="statsGrid"></div>
  </div>

  <div class="tab-content" id="tab-posts">
    <div class="posts-toolbar">
      <span id="postCount" style="font-size:0.85rem;color:#9494a8;">0 posts</span>
      <button class="btn-primary" onclick="showPostForm()"><i class="fas fa-plus"></i> New Post</button>
    </div>
    <div class="card" id="postsList"></div>
  </div>

  <div class="tab-content" id="tab-media">
    <div class="card">
      <h3><i class="fas fa-upload"></i> Upload Media</h3>
      <div class="upload-zone" id="uploadZone">
        <i class="fas fa-cloud-upload-alt"></i>
        <p>Drag & drop files or click to browse<br><small style="color:#6b6b80;">JPG, PNG, GIF, WEBP, MP4, WebM, MOV (max 500MB)</small></p>
        <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.mov">
      </div>
      <div class="progress-bar-wrap" id="progressWrap">
        <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
        <div class="progress-text" id="progressText">Uploading...</div>
      </div>
    </div>
    <div class="card">
      <h3><i class="fas fa-folder-open"></i> Uploaded Files <span id="fileCount" style="font-weight:400;color:#9494a8;font-size:0.8rem;"></span></h3>
      <div class="media-grid" id="mediaGrid"></div>
    </div>
  </div>

  <div class="tab-content" id="tab-settings">
    <div class="card">
      <h3><i class="fas fa-sliders-h"></i> Site Settings</h3>
      <div class="settings-grid" id="settingsForm"></div>
      <button class="btn-primary" onclick="saveSettings()" style="margin-top:1rem;"><i class="fas fa-save"></i> Save Settings</button>
    </div>
  </div>

  <div class="tab-content" id="tab-feedback">
    <div class="card" id="feedbackList"></div>
  </div>
</div>

<!-- Post Form Modal -->
<div class="form-overlay" id="postFormOverlay">
  <div class="form-modal">
    <h2 id="postFormTitle">New Post</h2>
    <form id="postForm">
      <input type="hidden" name="id" id="postId">
      <label>Title *</label>
      <input type="text" name="title" id="postTitle" required>
      <label>Caption</label>
      <textarea name="caption" id="postCaption"></textarea>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 0.75rem;">
        <div>
          <label>Event Date</label>
          <input type="text" name="event_date" id="postEventDate" placeholder="e.g. 15 MAY">
        </div>
        <div>
          <label>Event Time</label>
          <input type="text" name="event_time" id="postEventTime" placeholder="e.g. 7:00 PM">
        </div>
      </div>
      <label>Location</label>
      <input type="text" name="location" id="postLocation" placeholder="e.g. Church Hall">
      <label>Media URL</label>
      <input type="text" name="media_url" id="postMediaUrl" placeholder="/uploads/filename.jpg">
      <label>Thumbnail URL</label>
      <input type="text" name="thumbnail_url" id="postThumbnailUrl" placeholder="/uploads/thumb.jpg">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 0.75rem;">
        <div>
          <label>Media Type</label>
          <select name="media_type" id="postMediaType">
            <option value="image">Image</option>
            <option value="video">Video</option>
          </select>
        </div>
        <div>
          <label>Status</label>
          <select name="published" id="postPublished">
            <option value="1">Published</option>
            <option value="0">Draft</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="closePostForm()">Cancel</button>
        <button type="submit" class="btn-primary" id="postFormSubmit">Create Post</button>
      </div>
    </form>
  </div>
</div>

<script>
// ─── Tab Switching ───
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.add('active');

    if (btn.dataset.tab === 'dashboard') loadDashboard();
    if (btn.dataset.tab === 'posts') loadPosts();
    if (btn.dataset.tab === 'media') loadMedia();
    if (btn.dataset.tab === 'settings') loadSettings();
    if (btn.dataset.tab === 'feedback') loadFeedback();
  });
});

// ─── Toast ───
function showToast(msg, type = 'success') {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();
  const t = document.createElement('div');
  t.className = 'toast ' + type;
  t.textContent = msg;
  document.body.appendChild(t);
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3000);
}

// ─── Logout ───
async function logout() {
  try {
    await fetch('/api/admin/logout.php', { method: 'POST' });
  } catch {}
  location.reload();
}

// ─── Dashboard ───
async function loadDashboard() {
  try {
    const res = await fetch('/api/admin/storage.php');
    const d = await res.json();

    document.getElementById('storageBar').style.width = Math.min(d.usage_percent, 100) + '%';
    document.getElementById('storageUsed').textContent = d.total_size_mb + ' MB';
    document.getElementById('storageMax').textContent = '10 GB';

    const grid = document.getElementById('statsGrid');
    const items = [
      { num: d.images, label: 'Images' },
      { num: d.videos, label: 'Videos' },
      { num: d.posts, label: 'Posts' },
      { num: d.feedback, label: 'Feedback' },
      { num: d.bookings, label: 'Bookings' },
      { num: d.total_files, label: 'Total Files' }
    ];
    grid.innerHTML = items.map(i => `<div class="stat-item"><span class="num">${i.num}</span><span class="label">${i.label}</span></div>`).join('');
  } catch { showToast('Failed to load dashboard', 'error'); }
}

// ─── Posts ───
async function loadPosts() {
  try {
    const res = await fetch('/api/admin/posts.php');
    const posts = await res.json();
    const list = document.getElementById('postsList');
    document.getElementById('postCount').textContent = posts.length + ' posts';

    if (posts.length === 0) {
      list.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i>No posts yet</div>';
      return;
    }

    list.innerHTML = posts.map(p => `
      <div class="post-item">
        <div class="post-item-info">
          <h4>${esc(p.title)} <span class="badge ${p.published == 1 ? 'badge-published' : 'badge-draft'}">${p.published == 1 ? 'Published' : 'Draft'}</span></h4>
          <span>${esc(p.event_date || '')} ${esc(p.event_time || '')} ${esc(p.location || '')}</span>
        </div>
        <div class="post-item-actions">
          <button class="btn-edit" onclick="editPost(${p.id})"><i class="fas fa-edit"></i></button>
          <button class="btn-danger" onclick="deletePost(${p.id})"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `).join('');
  } catch { showToast('Failed to load posts', 'error'); }
}

function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

function showPostForm(post) {
  document.getElementById('postFormOverlay').classList.add('show');
  if (post) {
    document.getElementById('postFormTitle').textContent = 'Edit Post';
    document.getElementById('postFormSubmit').textContent = 'Update Post';
    document.getElementById('postId').value = post.id;
    document.getElementById('postTitle').value = post.title || '';
    document.getElementById('postCaption').value = post.caption || '';
    document.getElementById('postEventDate').value = post.event_date || '';
    document.getElementById('postEventTime').value = post.event_time || '';
    document.getElementById('postLocation').value = post.location || '';
    document.getElementById('postMediaUrl').value = post.media_url || '';
    document.getElementById('postThumbnailUrl').value = post.thumbnail_url || '';
    document.getElementById('postMediaType').value = post.media_type || 'image';
    document.getElementById('postPublished').value = post.published || '1';
  } else {
    document.getElementById('postFormTitle').textContent = 'New Post';
    document.getElementById('postFormSubmit').textContent = 'Create Post';
    document.getElementById('postForm').reset();
    document.getElementById('postId').value = '';
  }
}

function closePostForm() {
  document.getElementById('postFormOverlay').classList.remove('show');
}

async function editPost(id) {
  try {
    const res = await fetch('/api/admin/post.php?id=' + id);
    const post = await res.json();
    showPostForm(post);
  } catch { showToast('Failed to load post', 'error'); }
}

async function deletePost(id) {
  if (!confirm('Delete this post?')) return;
  try {
    const res = await fetch('/api/admin/post.php?id=' + id, { method: 'DELETE' });
    const d = await res.json();
    if (d.success) { showToast('Post deleted'); loadPosts(); }
    else showToast('Delete failed', 'error');
  } catch { showToast('Delete failed', 'error'); }
}

document.getElementById('postForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const fd = new FormData(this);
  const id = fd.get('id');
  const isEdit = id && id !== '';

  try {
    let res;
    if (isEdit) {
      const obj = {};
      fd.forEach((v, k) => { obj[k] = v; });
      res = await fetch('/api/admin/post.php?id=' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(obj)
      });
    } else {
      res = await fetch('/api/admin/posts.php', { method: 'POST', body: fd });
    }
    const d = await res.json();
    if (d.success || d.id) {
      showToast(isEdit ? 'Post updated' : 'Post created');
      closePostForm();
      loadPosts();
    } else showToast('Failed to save post', 'error');
  } catch { showToast('Failed to save post', 'error'); }
});

// Close modal on overlay click
document.getElementById('postFormOverlay').addEventListener('click', function(e) {
  if (e.target === this) closePostForm();
});

// ─── Media Upload ───
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');

uploadZone.addEventListener('click', () => fileInput.click());
uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
uploadZone.addEventListener('drop', e => { e.preventDefault(); uploadZone.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });
fileInput.addEventListener('change', () => handleFiles(fileInput.files));

async function handleFiles(files) {
  const pw = document.getElementById('progressWrap');
  const pf = document.getElementById('progressFill');
  const pt = document.getElementById('progressText');
  pw.classList.add('show');

  for (const file of files) {
    const fd = new FormData();
    fd.append('file', file);
    pt.textContent = `Uploading ${file.name}...`;
    pf.style.width = '50%';

    try {
      const res = await fetch('/api/admin/upload.php', { method: 'POST', body: fd });
      const d = await res.json();
      if (d.success) {
        pf.style.width = '100%';
        pt.textContent = `${file.name} uploaded`;
        showToast(`${file.name} uploaded`);
        loadMedia();
      } else {
        pt.textContent = `${file.name}: ${d.error}`;
        showToast(`${file.name}: ${d.error}`, 'error');
      }
    } catch {
      pt.textContent = `${file.name}: upload failed`;
      showToast(`${file.name}: upload failed`, 'error');
    }
  }

  setTimeout(() => { pw.classList.remove('show'); pf.style.width = '0'; }, 2000);
}

async function loadMedia() {
  try {
    const res = await fetch('/api/admin/media.php');
    const files = await res.json();
    document.getElementById('fileCount').textContent = `(${files.length} files, ${files.reduce((s, f) => s + f.size, 0) > 1048576 ? (files.reduce((s, f) => s + f.size, 0) / 1048576).toFixed(2) + ' MB' : (files.reduce((s, f) => s + f.size, 0) / 1024).toFixed(1) + ' KB'})`;
    const grid = document.getElementById('mediaGrid');
    if (files.length === 0) {
      grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><i class="fas fa-image"></i>No files uploaded</div>';
      return;
    }
    grid.innerHTML = files.map(f => {
      const isVideo = ['mp4','webm','mov'].includes(f.type);
      const sizeStr = f.size > 1048576 ? (f.size / 1048576).toFixed(1) + ' MB' : (f.size / 1024).toFixed(0) + ' KB';
      return `<div class="media-thumb" title="${f.name}">
        ${isVideo ? `<video src="${f.url}" muted></video><i class="fas fa-play" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:1.2rem;opacity:0.7;"></i>` : `<img src="${f.url}" loading="lazy">`}
        <div class="file-size">${sizeStr}</div>
      </div>`;
    }).join('');
  } catch { showToast('Failed to load media', 'error'); }
}

// ─── Settings ───
const SETTINGS_KEYS = ['events_count','members_count','followers_count','profile_bio'];

async function loadSettings() {
  try {
    const res = await fetch('/api/admin/settings.php');
    const s = await res.json();
    const form = document.getElementById('settingsForm');
    form.innerHTML = SETTINGS_KEYS.map(k => {
      const val = s[k] || '';
      const label = k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
      if (k === 'profile_bio') {
        return `<div class="setting-row"><label>${label}</label><textarea data-key="${k}">${esc(val)}</textarea></div>`;
      }
      return `<div class="setting-row"><label>${label}</label><input data-key="${k}" value="${esc(val)}"></div>`;
    }).join('');
  } catch { showToast('Failed to load settings', 'error'); }
}

async function saveSettings() {
  const data = {};
  document.querySelectorAll('#settingsForm [data-key]').forEach(el => {
    data[el.dataset.key] = el.value;
  });
  try {
    const res = await fetch('/api/admin/settings.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const d = await res.json();
    if (d.success) showToast('Settings saved');
    else showToast('Save failed', 'error');
  } catch { showToast('Save failed', 'error'); }
}

// ─── Feedback ───
async function loadFeedback() {
  try {
    const res = await fetch('/api/admin/feedback.php');
    const items = await res.json();
    const list = document.getElementById('feedbackList');
    if (items.length === 0) {
      list.innerHTML = '<div class="empty-state"><i class="fas fa-comment-slash"></i>No feedback yet</div>';
      return;
    }
    list.innerHTML = items.map(f => `
      <div class="feedback-item">
        <h4>${esc(f.name)} ${esc(f.surname || '')}</h4>
        <div class="meta">${f.created_at || ''} <span class="stars">${'<i class="fas fa-star"></i>'.repeat(f.rating)}${'<i class="far fa-star"></i>'.repeat(5 - f.rating)}</span></div>
        <p>${esc(f.message)}</p>
      </div>
    `).join('');
  } catch { showToast('Failed to load feedback', 'error'); }
}

// ─── Init ───
document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.admin-container.active')) {
    loadDashboard();
  }
});
</script>
</body>
</html>
