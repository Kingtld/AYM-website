const API_BASE = '/api';

function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

// ── Dark Mode Toggle ──
function initTheme() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    const icon = document.querySelector('#themeToggle i');
    if (icon) icon.className = saved === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute('data-theme') || 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    const icon = document.querySelector('#themeToggle i');
    if (icon) icon.className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

function initHamburger() {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    if (!hamburger || !mobileMenu) return;
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
        document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
    });
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('open');
            mobileMenu.classList.remove('open');
            document.body.style.overflow = '';
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    const toggle = document.getElementById('themeToggle');
    if (toggle) toggle.addEventListener('click', toggleTheme);
    initHamburger();
});

// ── Hero Stat Counter ──
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.dataset.target);
                animateCounter(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
}

function animateCounter(el, target) {
    const duration = 2000;
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    let step = 0;

    const timer = setInterval(() => {
        step++;
        current = Math.min(Math.round(increment * step), target);
        el.textContent = current >= 1000 ? (current / 1000).toFixed(1).replace('.0', '') + 'k' : current;
        if (step >= steps) {
            clearInterval(timer);
            el.textContent = target >= 1000 ? (target / 1000).toFixed(1).replace('.0', '') + 'k' : target;
        }
    }, duration / steps);
}

document.addEventListener('DOMContentLoaded', animateCounters);

// ── Scroll to Feed ──
function scrollToFeed() {
    const feed = document.querySelector('.feed');
    if (feed) feed.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Star Rating ──
function initStarRating() {
    const container = document.getElementById('starRating');
    if (!container) return;

    const stars = container.querySelectorAll('i');
    const hidden = document.getElementById('feedbackRating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const val = parseInt(star.dataset.value);
            hidden.value = val;
            stars.forEach((s, i) => {
                s.className = i < val ? 'fas fa-star active' : 'far fa-star';
            });
        });

        star.addEventListener('mouseenter', () => {
            const val = parseInt(star.dataset.value);
            stars.forEach((s, i) => {
                if (i < val) s.className = 'fas fa-star active';
                else if (!s.classList.contains('active')) s.className = 'far fa-star';
            });
        });

        container.addEventListener('mouseleave', () => {
            const val = parseInt(hidden.value);
            stars.forEach((s, i) => {
                s.className = i < val ? 'fas fa-star active' : 'far fa-star';
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', initStarRating);

// ── Feedback Handler ──
function handleFeedback(event) {
    event.preventDefault();

    const name = document.getElementById('feedbackName').value.trim();
    const surname = document.getElementById('feedbackSurname').value.trim();
    const rating = parseInt(document.getElementById('feedbackRating').value);
    const message = document.getElementById('feedbackMessage').value.trim();

    if (!rating) {
        showToast('Please select a rating', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('surname', surname);
    formData.append('rating', rating);
    formData.append('message', message);

    fetch('/api/feedback.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.admin) {
                window.location.href = '/admin/';
                return;
            }
            document.getElementById('feedbackFormElement').reset();
            document.getElementById('feedbackRating').value = 0;
            document.querySelectorAll('#starRating i').forEach(s => s.className = 'far fa-star');
            showToast(data.message || 'Thank you for your feedback!', 'success');
        })
        .catch(() => {
            showToast('Failed to send feedback. Try again.', 'error');
        });
}

// ── Copy Number ──
function copyNumber(num) {
    navigator.clipboard.writeText(num).then(() => {
        showToast('Number copied!', 'success');
    }).catch(() => {
        showToast('Failed to copy', 'error');
    });
}

// ── Existing: Booking ──
function bookEvent(eventName) {
    window.open('https://wa.me/27738463063', '_blank');
}

function closeModal() {
    const modal = document.getElementById('bookingModal');
    modal.style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('bookingModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

function createConfetti() {
    const colors = ['#007bff', '#28a745', '#ffc107', '#ffffff'];
    for (let i = 0; i < 50; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.left = Math.random() * 100 + 'vw';
        confetti.style.top = '-10px';
        confetti.style.borderRadius = '50%';
        confetti.style.zIndex = '9999';
        confetti.style.animation = `confettiFall ${2 + Math.random() * 3}s ease-out forwards`;
        document.body.appendChild(confetti);
        setTimeout(() => confetti.remove(), 5000);
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes confettiFall {
        to {
            transform: translateY(100vh) rotate(${Math.random() * 360}deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ── Existing: Like button ──
document.querySelectorAll('.post-left-actions i:first-child, .reel-actions i:first-child').forEach(heart => {
    heart.addEventListener('click', function() {
        this.style.color = this.style.color === 'rgb(225, 48, 108)' ? '' : '#e1306c';
        if (this.style.color === 'rgb(225, 48, 108)') {
            this.style.animation = 'heartbeat 0.5s ease';
            setTimeout(() => { this.style.animation = ''; }, 500);
        }
    });
});

// ── Existing: Tab switching ──
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(tab).classList.add('active');
    });
});

// ── Existing: Follow button ──
document.querySelectorAll('.follow-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (this.textContent === 'Follow') {
            this.textContent = 'Following';
            this.style.background = '#8e8e8e';
        } else {
            this.textContent = 'Follow';
            this.style.background = '#0095f6';
        }
    });
});

// ── Existing: Story scroll ──
const storiesBar = document.querySelector('.stories-bar');
if (storiesBar) {
    let isDown = false;
    let startX;
    let scrollLeft;

    storiesBar.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - storiesBar.offsetLeft;
        scrollLeft = storiesBar.scrollLeft;
    });

    storiesBar.addEventListener('mouseleave', () => isDown = false);
    storiesBar.addEventListener('mouseup', () => isDown = false);

    storiesBar.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - storiesBar.offsetLeft;
        const walk = (x - startX) * 2;
        storiesBar.scrollLeft = scrollLeft - walk;
    });
}

// ── Existing: Grid hover ──
document.querySelectorAll('.grid-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        const icon = this.querySelector('i');
        if (icon) {
            icon.style.transform = 'scale(1.2) rotate(10deg)';
            icon.style.transition = 'all 0.3s ease';
        }
    });
    item.addEventListener('mouseleave', function() {
        const icon = this.querySelector('i');
        if (icon) icon.style.transform = 'scale(1) rotate(0deg)';
    });
});

// ── Scroll Reveal ──
function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', initScrollReveal);

// ── Existing: Page fade-in ──
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    setTimeout(() => { document.body.style.opacity = '1'; }, 100);
});

// ── Existing: Search animation ──
document.querySelectorAll('.search-input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.transition = 'transform 0.3s ease';
    });
    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
    });
});

// ── Existing: Story circle click ──
document.querySelectorAll('.story-circle').forEach(circle => {
    circle.addEventListener('click', function() {
        const avatar = this.querySelector('.story-avatar');
        avatar.style.transform = 'scale(0.9)';
        setTimeout(() => { avatar.style.transform = 'scale(1)'; }, 200);
    });
});

// ── Existing: Bookmark toggle ──
document.querySelectorAll('.post-actions > i:last-child').forEach(bookmark => {
    bookmark.addEventListener('click', function() {
        if (this.classList.contains('fas')) {
            this.classList.remove('fas');
            this.classList.add('far');
        } else {
            this.classList.remove('far');
            this.classList.add('fas');
            this.style.color = '#ffc107';
            setTimeout(() => { this.style.color = ''; }, 1000);
        }
    });
});

// ── Lightbox ──
function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    const content = lightbox.querySelector('.lightbox-content');
    const counter = lightbox.querySelector('.lightbox-counter');
    const prevBtn = lightbox.querySelector('.lightbox-nav.prev');
    const nextBtn = lightbox.querySelector('.lightbox-nav.next');
    const closeBtn = lightbox.querySelector('.lightbox-close');

    let items = [];
    let currentIndex = 0;

    function open(index) {
        currentIndex = index;
        const item = items[currentIndex];
        if (!item) return;

        const isVideo = item.tagName === 'VIDEO';
        content.innerHTML = '';

        if (isVideo) {
            const video = document.createElement('video');
            video.src = item.querySelector('source') ? item.querySelector('source').src : item.src;
            video.controls = true;
            video.autoplay = true;
            content.appendChild(video);
        } else {
            const img = document.createElement('img');
            img.src = item.src;
            img.alt = item.alt || '';
            content.appendChild(img);
        }

        counter.textContent = `${currentIndex + 1} / ${items.length}`;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        content.innerHTML = '';
    }

    function prev() {
        if (items.length === 0) return;
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        open(currentIndex);
    }

    function next() {
        if (items.length === 0) return;
        currentIndex = (currentIndex + 1) % items.length;
        open(currentIndex);
    }

    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', prev);
    nextBtn.addEventListener('click', next);

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) close();
    });

    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'ArrowRight') next();
    });

    // Also allow clicking the 5 hero reels to open lightbox
    document.querySelectorAll('.reel[data-media]').forEach(reel => {
        reel.addEventListener('click', (e) => {
            if (e.target.closest('.reel-download-btn')) return;
            if (e.target.closest('.reel-actions')) return;
            const src = reel.dataset.media;
            content.innerHTML = '';
            if (src.endsWith('.mp4')) {
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.autoplay = true;
                content.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = src;
                content.appendChild(img);
            }
            counter.textContent = '1 / 1';
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });
}

document.addEventListener('DOMContentLoaded', initLightbox);

// ── Play reel media (called from reel-card onclick) ──
function playReelMedia(card) {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    const content = lightbox.querySelector('.lightbox-content');
    const counter = lightbox.querySelector('.lightbox-counter');
    const src = card.dataset.media;

    content.innerHTML = '';

    if (src.endsWith('.mp4')) {
        const video = document.createElement('video');
        video.src = src;
        video.controls = true;
        video.autoplay = true;
        content.appendChild(video);
    } else {
        const img = document.createElement('img');
        img.src = src;
        img.alt = '';
        content.appendChild(img);
    }

    counter.textContent = '1 / 1';
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// ── Share Page ──
function sharePage(event) {
    event.stopPropagation();
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({ title: document.title, url: url }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url).then(() => {
            showToast('Link copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Failed to copy link', 'error');
        });
    }
}


