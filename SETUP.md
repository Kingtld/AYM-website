# AYM Cloudflare Deployment Guide

## Prerequisites
- Cloudflare account (you have one)
- Node.js installed
- Git (optional but recommended)

## Step 1: Install Wrangler CLI

```bash
npm install -g wrangler
wrangler login
```

## Step 2: Create D1 Database

```bash
wrangler d1 create aym-db
```

This returns a `database_id`. Copy it. Open `workers/wrangler.toml` and paste it into the `database_id` field.

Initialize the schema:

```bash
wrangler d1 execute aym-db --file=workers/schema.sql
```

## Step 3: Create R2 Bucket

```bash
wrangler r2 bucket create aym-media
```

## Step 4: Set Worker Secrets

```bash
wrangler secret put JWT_SECRET        # Any random string, keep it safe
wrangler secret put ADMIN_NAME        # Jehofa
wrangler secret put ADMIN_SURNAME     # Mmabaledi
wrangler secret put ADMIN_PHRASE      # reyago boka morena
wrangler secret put ADMIN_RATING      # 2
```

## Step 5: Deploy Worker

```bash
cd workers
wrangler deploy
```

## Step 6: Deploy to Cloudflare Pages

**Option A — Git integration (recommended):**
1. Push repo to GitHub/GitLab
2. In Cloudflare Dashboard → Pages → Create a project
3. Connect your Git repo
4. Build settings: Framework = None, Build output = root directory
5. Set **Root directory** (leave blank if site is at repo root)

**Option B — Direct upload:**
1. In Cloudflare Dashboard → Pages → Create a project
2. Upload the entire AYM folder

## Step 7: Route Admin Panel Through Worker

In Cloudflare Dashboard → Pages → your project → Custom domains:
1. Add a Worker Route: `/admin*` → `aym-api`
2. Add a Worker Route: `/api/*` → `aym-api`

This ensures all admin and API traffic goes through the Worker for auth.

## Step 8: Configure Cloudflare Access (Optional)

If you want extra auth for `/admin/`:
1. Cloudflare Dashboard → Zero Trust → Access → Applications
2. Add a self-hosted application
3. Set domain to your Pages domain
4. Set path to `/admin/`
5. Add yourself as the only user (email OTP is easiest)

This is optional — the secret feedback auth already secures the admin panel.

## File Structure After Setup

```
AYM/
├── admin/
│   ├── admin.css           # Admin panel styles (reference)
│   └── admin.js            # Admin panel JS (reference)
├── css/
│   └── style.css           # Site styles with dark mode
├── js/
│   └── script.js           # Site JS with feedback handler + dark mode
├── workers/
│   ├── wrangler.toml       # Worker config (edit database_id)
│   ├── api.js              # Main Worker API
│   └── schema.sql          # D1 database schema
├── index.html              # Homepage with hero
├── memories.html           # Reels page
├── profile.html            # Profile page
├── about.html              # About page + feedback form
└── SETUP.md                # This guide
```
