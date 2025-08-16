
function renderCards(type, data) {
    if (!data.length) return '<div style="text-align:center;color:#888;">No data found.</div>';
    let html = '<div class="card-list">';
function timeAgo(dateStr) {
    const date = new Date(dateStr.replace(/-/g, '/'));
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    if (isNaN(diff)) return dateStr;
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff/60) + ' min ago';
    if (diff < 86400) return Math.floor(diff/3600) + ' hours ago';
    if (diff < 604800) return Math.floor(diff/86400) + ' days ago';
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

    if (type === 'vendor') {
        data.forEach(v => {
            html += `<div class="card vendor-card">
                <div class="vendor-card-header">
                    <div class="vendor-card-title">${v.name}</div>
                    <div class="vendor-card-meta">${v.location || ''}</div>
                </div>
                <div class="vendor-card-body">
                    <div class="vendor-card-desc">${v.offering || ''}</div>
                    ${v.discount ? `<div class="vendor-card-discount">${v.discount}</div>` : ''}
                </div>
            </div>`;
        });
    } else if (type === 'gallery') {
        // Show both gallery and video content types
        data.forEach(item => {
            if (item.content_type === 'gallery') {
                html += `<div class="card youtube-card gallery-youtube-card">
                    <div class="youtube-card-thumb-wrap">
                        ${Array.isArray(item.images) && item.images.length > 0 ? `
                            <div class='carousel-inner'>
                                ${item.images.map((img, idx) => `
                                    <img src='${img.image_src}' alt='${img.title || item.title}' class='carousel-img youtube-card-thumb${idx === 0 ? ' active' : ''}'>
                                `).join('')}
                                <button class='carousel-prev' style='display:${item.images.length > 1 ? 'block' : 'none'};'>&lt;</button>
                                <button class='carousel-next' style='display:${item.images.length > 1 ? 'block' : 'none'};'>&gt;</button>
                            </div>
                        ` : `<div class="youtube-card-thumb youtube-card-thumb-placeholder">No images</div>`}
                    </div>
                    <div class="youtube-card-body">
                        <div class="youtube-card-avatar">
                            ${item.title ? item.title.charAt(0).toUpperCase() : 'G'}
                        </div>
                        <div class="youtube-card-content">
                            <div class="youtube-card-title">${item.title}</div>
                            <div class="youtube-card-meta">${timeAgo(item.created_at)}</div>
                            <div class="youtube-card-desc">${item.caption ? item.caption : ''}</div>
                        </div>
                    </div>
                </div>`;
            } else if (item.content_type === 'video') {
                html += `<div class="card youtube-card">
                    <div class="youtube-card-thumb-wrap">
                        ${item.embedded_src ? `<iframe src='${item.embedded_src}' frameborder='0' allowfullscreen class='youtube-card-thumb'></iframe>` : '<div class="youtube-card-thumb youtube-card-thumb-placeholder">No video</div>'}
                    </div>
                    <div class="youtube-card-body">
                        <div class="youtube-card-avatar">
                            <span style='font-size:1.3rem;'>🎬</span>
                        </div>
                        <div class="youtube-card-content">
                            <div class="youtube-card-title">${item.title}</div>
                            <div class="youtube-card-meta">${timeAgo(item.created_at)}</div>
                        </div>
                    </div>
                </div>`;
            }
        });

        // Carousel JS logic (after rendering)
        setTimeout(() => {
            document.querySelectorAll('.gallery-carousel').forEach(carousel => {
                const imgs = carousel.querySelectorAll('.carousel-img');
                if (imgs.length < 2) return;
                let idx = 0;
                const prevBtn = carousel.querySelector('.carousel-prev');
                const nextBtn = carousel.querySelector('.carousel-next');
                function showImg(i) {
                    imgs.forEach((img, j) => img.style.display = (j === i ? 'block' : 'none'));
                }
                prevBtn && prevBtn.addEventListener('click', e => {
                    idx = (idx - 1 + imgs.length) % imgs.length;
                    showImg(idx);
                });
                nextBtn && nextBtn.addEventListener('click', e => {
                    idx = (idx + 1) % imgs.length;
                    showImg(idx);
                });
            });
        }, 0);
    } else if (type === 'event') {
        data.forEach(e => {
            // Format date for badge (e.g., "Jul 30")
            let eventDate = e.event_date ? new Date(e.event_date) : (e.created_at ? new Date(e.created_at) : null);
            let badgeMonth = eventDate ? eventDate.toLocaleString('default', { month: 'short' }) : '';
            let badgeDay = eventDate ? eventDate.getDate() : '';
            html += `<div class="card eventbrite-card">
                <div class="eventbrite-card-banner-wrap">
                    ${e.banner_url ? `<img src='${e.banner_url}' alt='${e.title}' class='eventbrite-card-banner-img'>` : `<div class='eventbrite-card-banner-placeholder'><svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#bfc8ff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="4"/><path d="M16 2v4M8 2v4M3 10h18"/><circle cx="8.5" cy="16.5" r="1.5"/><circle cx="15.5" cy="16.5" r="1.5"/></svg></div>`}
                    <div class="eventbrite-card-date-badge">
                        <div class="eventbrite-card-date-month">${badgeMonth}</div>
                        <div class="eventbrite-card-date-day">${badgeDay}</div>
                    </div>
                </div>
                <div class="eventbrite-card-body">
                    <div class="eventbrite-card-title">${e.title}</div>
                    <div class="eventbrite-card-meta">
                        <span class="eventbrite-card-location">${e.location || ''}</span>
                        <span class="eventbrite-card-created">${e.created_at ? timeAgo(e.created_at) : ''}</span>
                    </div>
                    <div class="eventbrite-card-desc">${e.description ? e.description : 'Event details coming soon.'}</div>
                </div>
            </div>`;
        });
    } else {
        data.forEach(n => {
            html += `<div class="card news-card-yahoo">
                ${n.image_url ? `<div class="news-card-yahoo-img-wrap"><img src='${n.image_url}' class="news-card-yahoo-img"></div>` : ''}
                <div class="news-card-yahoo-content">
                    <h3 class="news-card-yahoo-title">${n.title}</h3>
                    <div class="news-card-yahoo-meta">${n.published_at ? timeAgo(n.published_at) : (n.created_at ? timeAgo(n.created_at) : '')}</div>
                    <div class="news-card-yahoo-desc">${n.body ? n.body.substring(0,220) + '...' : (n.article_body ? n.article_body.substring(0,220) + '...' : '')}</div>
                </div>
            </div>`;
        });
    }
    html += '</div>';
    return html;
}

function loadTab(type) {
    var tabContent = document.getElementById('tab-content');
    tabContent.style.opacity = 0;
    setTimeout(function() {
        tabContent.innerHTML = `<div id="dashboard-loading" style="display:flex;align-items:center;justify-content:center;height:180px;">
            <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg" style="animation:spin 1s linear infinite;">
                <circle cx="27" cy="27" r="22" stroke="#6373ff" stroke-width="6" opacity="0.2"/>
                <path d="M49 27a22 22 0 1 1-44 0" stroke="#6373ff" stroke-width="6" stroke-linecap="round"/>
            </svg>
        </div>`;
        var url = (type === 'news') ? 'api/news_data.php' : ('api/dashboard_data.php?type=' + encodeURIComponent(type));
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.status === 'success') {
                        tabContent.innerHTML = renderCards(type, res.data);
                    } else {
                        tabContent.innerHTML = '<div style="color:red;">Failed to load data.</div>';
                    }
                } catch (e) {
                    tabContent.innerHTML = '<div style="color:red;">Error loading data.</div>';
                }
                // Fade in after content is set
                setTimeout(function() {
                    tabContent.style.opacity = 1;
                }, 30);
            }
        };
        xhr.send();
    }, 200);
}

document.querySelectorAll('.tab-bar button').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.tab-bar button').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        loadTab(this.getAttribute('data-type'));
    });
});

// Initial load
loadTab('news');