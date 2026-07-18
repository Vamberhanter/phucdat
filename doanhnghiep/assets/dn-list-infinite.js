/**
 * Infinite scroll for DN directory list (.main-center). Keeps pagination links.
 */
(function initDnListInfiniteScroll() {
    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    ready(function () {
        var cfg = window.dnttvnDnList;
        var list = document.getElementById('doanh-nghiep-list');
        if (!cfg || !list || list.getAttribute('data-dn-infinite') !== '1') {
            return;
        }

        var cards = list.querySelector('.dn-list-cards');
        var sentinel = list.querySelector('.dn-infinite-sentinel');
        var statusEl = list.querySelector('.dn-infinite-status');
        var mainCenter = list.closest('.main-center') || list;
        if (!cards || !sentinel) {
            return;
        }

        var page = parseInt(list.getAttribute('data-dn-page') || '1', 10) || 1;
        var maxPages = parseInt(list.getAttribute('data-dn-max-pages') || '1', 10) || 1;
        var filters = {};
        try {
            filters = JSON.parse(list.getAttribute('data-dn-filters') || '{}') || {};
        } catch (e) {
            filters = {};
        }

        var loading = false;
        var done = page >= maxPages;

        function setStatus(show, text) {
            if (!statusEl) {
                return;
            }
            if (text) {
                statusEl.textContent = text;
            }
            if (show) {
                statusEl.removeAttribute('hidden');
            } else {
                statusEl.setAttribute('hidden', '');
            }
        }

        function updatePaginationCurrent(current) {
            var pag = list.querySelector('.pagination');
            if (!pag) {
                return;
            }
            pag.querySelectorAll('a.page-numbers, span.page-numbers').forEach(function (el) {
                if (el.classList.contains('prev') || el.classList.contains('next') || el.classList.contains('dots')) {
                    return;
                }
                var n = parseInt((el.textContent || '').trim(), 10);
                if (!n) {
                    return;
                }
                el.classList.toggle('dn-page-loaded', n === current);
            });
        }

        function loadNext() {
            if (loading || done) {
                return;
            }
            if (page >= maxPages) {
                done = true;
                setStatus(false);
                return;
            }

            loading = true;
            setStatus(true, '\u0110ang t\u1ea3i th\u00eam...');

            var body = new FormData();
            body.append('action', cfg.action);
            body.append('nonce', cfg.nonce);
            body.append('paged', String(page + 1));
            Object.keys(filters).forEach(function (key) {
                if (filters[key] != null && filters[key] !== '') {
                    body.append(key, filters[key]);
                }
            });

            fetch(cfg.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (json) {
                    if (!json || !json.success || !json.data) {
                        throw new Error('load_failed');
                    }
                    var data = json.data;
                    if (data.html) {
                        cards.insertAdjacentHTML('beforeend', data.html);
                    }
                    page = data.paged || page + 1;
                    maxPages = data.max_pages || maxPages;
                    list.setAttribute('data-dn-page', String(page));
                    list.setAttribute('data-dn-max-pages', String(maxPages));
                    updatePaginationCurrent(page);
                    done = !data.has_more || page >= maxPages;
                    setStatus(false);
                    if (done && sentinel && sentinel.parentNode) {
                        sentinel.parentNode.removeChild(sentinel);
                    }
                })
                .catch(function () {
                    setStatus(true, 'Kh\u00f4ng t\u1ea3i \u0111\u01b0\u1ee3c. Cu\u1ed9n l\u1ea1i \u0111\u1ec3 th\u1eed.');
                })
                .finally(function () {
                    loading = false;
                });
        }

        if (done) {
            if (sentinel && sentinel.parentNode) {
                sentinel.parentNode.removeChild(sentinel);
            }
            return;
        }

        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(
                function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            loadNext();
                        }
                    });
                },
                { root: mainCenter, rootMargin: '120px 0px', threshold: 0 }
            );
            io.observe(sentinel);
        } else {
            mainCenter.addEventListener('scroll', function () {
                if (done || loading) {
                    return;
                }
                if (mainCenter.scrollTop + mainCenter.clientHeight >= mainCenter.scrollHeight - 160) {
                    loadNext();
                }
            });
        }
    });
})();
