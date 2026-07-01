jQuery(document).ready(function ($) {
    // Media Uploader for Hình ảnh phụ
    var mediaUploader;

    $('#upload_hinh_anh_phu').on('click', function (e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Chọn hình ảnh phụ',
            button: {
                text: 'Sử dụng hình ảnh này'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#hinh_anh_phu').val(attachment.id);

            // Show preview
            var thumbUrl = attachment.url;
            if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
                thumbUrl = attachment.sizes.thumbnail.url;
            }
            var previewHtml = '<div style="margin-top: 10px;" class="hinh-anh-phu-preview">';
            previewHtml += '<img src="' + thumbUrl + '" style="max-width: 150px; height: auto; border: 1px solid #ddd; padding: 5px;" />';
            previewHtml += '<p style="margin-top: 5px; color: #666; font-size: 12px;">ID: ' + attachment.id + '</p>';
            previewHtml += '</div>';

            // Remove old preview if exists
            $('.hinh-anh-phu-preview').remove();
            $('#hinh_anh_phu').after(previewHtml);
        });

        mediaUploader.open();
    });

    // Media uploader for gallery images (multiple)
    var galleryUploader;

    $('#upload_gallery_images').on('click', function (e) {
        e.preventDefault();

        if (galleryUploader) {
            galleryUploader.open();
            return;
        }

        galleryUploader = wp.media({
            title: 'Chọn nhiều hình cho thư viện',
            button: {
                text: 'Sử dụng các hình này'
            },
            multiple: true,
            library: {
                type: 'image'
            }
        });

        galleryUploader.on('select', function () {
            var selection = galleryUploader.state().get('selection').toJSON();
            var ids = [];
            var previewHtml = '';

            selection.forEach(function (attachment) {
                ids.push(attachment.id);
                var thumbUrl = attachment.url;
                if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
                    thumbUrl = attachment.sizes.thumbnail.url;
                }
                previewHtml += '<div><img src="' + thumbUrl + '" style="max-width: 80px; height: auto; border:1px solid #ddd; padding:3px; background:#fff;" /></div>';
            });

            $('#gallery_images').val(ids.join(','));
            $('#gallery_images_preview').html(previewHtml);
        });

        galleryUploader.open();
    });

    // Populate Quick Edit fields
    var $inlineEditor = $('#inline-edit');
    var $bulkEditor = $('#bulk-edit');

    // Quick Edit
    $inlineEditor.on('click', '.editinline', function () {
        var $row = $(this).closest('tr');
        var postId = $row.attr('id').replace('post-', '');

        var nganh_hang = $row.find('.column-nganh_hang').text().trim();
        var khu_vuc = $row.find('.column-khu_vuc').text().trim();

        $inlineEditor.find('input[name="nganh_hang"]').val(nganh_hang);
        $inlineEditor.find('input[name="khu_vuc"]').val(khu_vuc);
    });

    // Bulk Edit - Add fields to form
    if ($bulkEditor.length) {
        $bulkEditor.find('.inline-edit-col-right').prepend(
            '<div class="inline-edit-col">' +
            '<div class="inline-edit-group wp-clearfix">' +
            '<label class="inline-edit-status alignleft">' +
            '<span class="title">Ngành hàng</span>' +
            '<input type="text" name="_nganh_hang" value="" />' +
            '</label>' +
            '</div>' +
            '<div class="inline-edit-group wp-clearfix">' +
            '<label class="inline-edit-status alignleft">' +
            '<span class="title">Khu vực</span>' +
            '<input type="text" name="_khu_vuc" value="" />' +
            '</label>' +
            '</div>' +
            '</div>'
        );
    }

    // Ô nội dung nhỏ: hiển thị WYSIWYG (contenteditable) để ẩn HTML, chỉ thấy chữ đã định dạng; đồng bộ với textarea ẩn khi submit.
    (function () {
        var toolbarHtml = '<div class="dnttvn-content-toolbar" role="toolbar">' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="b" title="In đậm">B</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="i" title="In nghiêng">I</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="u" title="Gạch chân">U</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="s" title="Gạch ngang chữ">S</button>' +
            '<span class="dnttvn-toolbar-sep" aria-hidden="true">|</span>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn dnttvn-toolbar-link" data-cmd="link" title="Chèn link">Link</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="ul" title="Danh sách gạch đầu dòng">• List</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="ol" title="Danh sách đánh số">1. List</button>' +
            '<span class="dnttvn-toolbar-sep" aria-hidden="true">|</span>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="blockquote" title="Trích dẫn">"</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="h3" title="Tiêu đề nhỏ (H3)">H3</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="code" title="Mã/Code">&lt;/&gt;</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="hr" title="Đường kẻ ngang">—</button>' +
            '<span class="dnttvn-toolbar-sep" aria-hidden="true">|</span>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="table" title="Chèn bảng">Bảng</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="addrow" title="Thêm dòng">+ Dòng</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="addcol" title="Thêm cột">+ Cột</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="deleterow" title="Xóa dòng">Xóa dòng</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="deletecol" title="Xóa cột">Xóa cột</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="cellcolor" title="Màu nền ô">Màu ô</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="textcolor" title="Màu chữ">Màu chữ</button>' +
            '<button type="button" class="button button-small dnttvn-toolbar-btn" data-cmd="deletetable" title="Xóa bảng">Xóa bảng</button>' +
            '</div>';

        function getWrapForToolbar($toolbar) {
            return $toolbar.closest('.dnttvn-content-toolbar-wrap');
        }
        function getCell(node, ed) {
            var n = node && node.nodeType === 3 ? node.parentNode : node;
            while (n && n !== ed) {
                if (n.nodeName === 'TD' || n.nodeName === 'TH') return n;
                n = n.parentNode;
            }
            return null;
        }
        function getTableFromSelection(ed) {
            var sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return null;
            var start = sel.getRangeAt(0).startContainer;
            var cell = getCell(start, ed);
            return cell ? cell.closest('table') : null;
        }
        function getRowFromSelection(ed) {
            var sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return null;
            var start = sel.getRangeAt(0).startContainer;
            var cell = getCell(start, ed);
            return cell ? cell.closest('tr') : null;
        }
        function deleteTable(ed) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào bảng cần xóa rồi bấm Xóa bảng.'); return; }
            table.parentNode.removeChild(table);
        }
        function addRow(ed) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào bảng rồi bấm Thêm dòng.'); return; }
            var tbody = table.querySelector('tbody') || table;
            var currentRow = getRowFromSelection(ed);
            var refRow = currentRow || tbody.querySelector('tr');
            if (!refRow) return;
            var newTr = document.createElement('tr');
            var cells = refRow.querySelectorAll('td, th');
            for (var i = 0; i < cells.length; i++) {
                var td = document.createElement(cells[i].nodeName);
                td.innerHTML = '&nbsp;';
                newTr.appendChild(td);
            }
            refRow.parentNode.insertBefore(newTr, refRow.nextSibling);
        }
        function deleteRow(ed) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào bảng rồi bấm Xóa dòng.'); return; }
            var row = getRowFromSelection(ed);
            if (!row) { alert('Đặt con trỏ vào một ô trong dòng cần xóa.'); return; }
            var tbody = table.querySelector('tbody') || table;
            var rows = tbody.querySelectorAll('tr');
            if (rows.length <= 1) { alert('Bảng chỉ còn một dòng, không thể xóa.'); return; }
            row.parentNode.removeChild(row);
        }
        function addCol(ed) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào bảng rồi bấm Thêm cột.'); return; }
            var cell = getCell(window.getSelection().getRangeAt(0).startContainer, ed);
            if (!cell) { alert('Đặt con trỏ vào một ô trong bảng.'); return; }
            var row = cell.closest('tr');
            var cols = row.querySelectorAll('td, th');
            var colIndex = -1;
            for (var i = 0; i < cols.length; i++) { if (cols[i] === cell) { colIndex = i; break; } }
            if (colIndex === -1) return;
            var tbody = table.querySelector('tbody') || table;
            var allRows = tbody.querySelectorAll('tr');
            for (var r = 0; r < allRows.length; r++) {
                var cells = allRows[r].querySelectorAll('td, th');
                var newCell = document.createElement(cell.nodeName);
                newCell.innerHTML = '&nbsp;';
                var ref = cells[colIndex] || null;
                allRows[r].insertBefore(newCell, ref);
            }
        }
        function deleteCol(ed) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào bảng rồi bấm Xóa cột.'); return; }
            var cell = getCell(window.getSelection().getRangeAt(0).startContainer, ed);
            if (!cell) { alert('Đặt con trỏ vào một ô trong cột cần xóa.'); return; }
            var row = cell.closest('tr');
            var cells = row.querySelectorAll('td, th');
            var colIndex = Array.prototype.indexOf.call(cells, cell);
            if (colIndex === -1) return;
            var tbody = table.querySelector('tbody') || table;
            var allRows = tbody.querySelectorAll('tr');
            for (var r = 0; r < allRows.length; r++) {
                var rowCells = allRows[r].querySelectorAll('td, th');
                if (rowCells[colIndex]) rowCells[colIndex].parentNode.removeChild(rowCells[colIndex]);
            }
        }
        var COLOR_PALETTE = [
            '#ffffff', '#f5f5f5', '#e8e8e8', '#d0d0d0', '#a0a0a0', '#606060', '#333333', '#000000',
            '#fff3cd', '#ffeaa7', '#fdcb6e', '#e17055', '#d63031', '#e84393', '#a29bfe', '#6c5ce7',
            '#00b894', '#00cec9', '#0984e3', '#74b9ff', '#81ecec', '#55efc4', '#00b894', '#2d3436'
        ];
        function showColorPalette($anchor, onPick) {
            $('.dnttvn-color-palette').remove();
            var $palette = $('<div class="dnttvn-color-palette" role="dialog" aria-label="Chọn màu"></div>');
            var $grid = $('<div class="dnttvn-color-palette-grid"></div>');
            COLOR_PALETTE.forEach(function (hex) {
                var $swatch = $('<button type="button" class="dnttvn-color-swatch" title="' + hex + '" style="background-color:' + hex + '"></button>');
                $swatch.on('click', function (e) { e.preventDefault(); onPick(hex); $palette.remove(); });
                $grid.append($swatch);
            });
            $palette.append($grid);
            var $custom = $('<button type="button" class="button button-small dnttvn-color-custom">Tùy chọn...</button>');
            $custom.on('click', function (e) {
                e.preventDefault();
                var c = prompt('Nhập mã màu (hex, ví dụ #f0f0f0):', '#f5f5f5');
                if (c !== null && c !== '') {
                    c = c.trim();
                    if (c.indexOf('#') !== 0) c = '#' + c;
                    onPick(c);
                }
                $palette.remove();
            });
            $palette.append($custom);
            $('body').append($palette);
            var rect = $anchor[0].getBoundingClientRect();
            $palette.css({
                position: 'fixed',
                top: (rect.bottom + 2) + 'px',
                left: rect.left + 'px',
                zIndex: 100100
            });
            $(document).one('click', function () { $palette.remove(); });
            $palette.on('click', function (e) { e.stopPropagation(); });
        }
        function cellBackColor(ed, color) {
            if (!color) return;
            var startCell = getCell(window.getSelection().getRangeAt(0).startContainer, ed);
            if (!startCell) return;
            startCell.style.backgroundColor = color;
        }
        function applyCellColorWithPalette(ed, ta, $btn) {
            var table = getTableFromSelection(ed);
            if (!table) { alert('Đặt con trỏ vào ô cần đổi màu nền.'); return; }
            var startCell = getCell(window.getSelection().getRangeAt(0).startContainer, ed);
            if (!startCell) { alert('Đặt con trỏ vào một ô trong bảng.'); return; }
            showColorPalette($btn, function (color) {
                cellBackColor(ed, color);
                syncEditableToTa(ed, ta);
                if (ta) $(ta).trigger('input');
            });
        }
        function applyTextColorWithPalette(ed, ta, $btn, syncEditableToTa) {
            showColorPalette($btn, function (color) {
                document.execCommand('foreColor', false, color);
                syncEditableToTa(ed, ta);
                if (ta) $(ta).trigger('input');
            });
        }
        function getEditable($wrap) {
            return $wrap.find('.dnttvn-rich-editor')[0];
        }
        function getTextarea($wrap) {
            return $wrap.find('textarea.structured-content-item')[0];
        }

        function taToHtml(val) {
            if (!val || !val.trim()) return '';
            var s = val.trim();
            if (s.indexOf('<') !== -1) return s;
            var escaped = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            return '<p>' + escaped.replace(/\n/g, '</p><p>') + '</p>';
        }
        function syncEditableToTa(ed, ta) {
            if (!ta) return;
            ta.value = ed ? ed.innerHTML : '';
        }
        function syncTaToEditable(ta, ed) {
            if (!ed) return;
            ed.innerHTML = ta && ta.value ? taToHtml(ta.value) : '';
        }

        function ensureToolbar(ta) {
            if (!ta || ta.getAttribute('data-dnttvn-toolbar') === '1') return;
            var $ta = $(ta);
            if ($ta.closest('.dnttvn-content-toolbar-wrap').length) return;
            var $wrap = $('<div class="dnttvn-content-toolbar-wrap"></div>');
            $ta.wrap($wrap);
            $ta.before(toolbarHtml);
            var richWrap = $('<div class="dnttvn-rich-editor-wrap"></div>');
            var ed = document.createElement('div');
            ed.className = 'dnttvn-rich-editor';
            ed.contentEditable = 'true';
            ed.setAttribute('data-placeholder', 'Nhập nội dung chi tiết...');
            richWrap.append(ed);
            $ta.after(richWrap);
            $ta.addClass('dnttvn-sync-ta');
            $ta.css({ position: 'absolute', left: '-9999px', width: '1px', height: '1px', opacity: 0, pointerEvents: 'none' });
            syncTaToEditable(ta, ed);
            ta.setAttribute('data-dnttvn-toolbar', '1');
            $(ed).on('input blur', function () {
                syncEditableToTa(ed, ta);
                $ta.trigger('input');
            });
        }

        function runEnsureToolbarForAll() {
            $('textarea.structured-content-item').each(function () { ensureToolbar(this); });
        }
        $(document).ready(function () {
            runEnsureToolbarForAll();
            var mo = new MutationObserver(function () { runEnsureToolbarForAll(); });
            var root = document.getElementById('structured-content-items');
            if (root) mo.observe(root, { childList: true, subtree: true });
        });
        $(document).on('focus', 'textarea.structured-content-item', function () {
            ensureToolbar(this);
            var $w = $(this).closest('.dnttvn-content-toolbar-wrap');
            var ed = $w.find('.dnttvn-rich-editor')[0];
            if (ed) ed.focus();
        });

        $(document).on('click', '.dnttvn-content-toolbar button[data-cmd]', function (e) {
            e.preventDefault();
            var $wrap = getWrapForToolbar($(this).closest('.dnttvn-content-toolbar'));
            var ed = getEditable($wrap);
            var ta = getTextarea($wrap);
            var cmd = $(this).data('cmd');
            if (ed) {
                ed.focus();
                if (cmd === 'b') document.execCommand('bold', false, null);
                else if (cmd === 'i') document.execCommand('italic', false, null);
                else if (cmd === 'u') document.execCommand('underline', false, null);
                else if (cmd === 's') document.execCommand('strikeThrough', false, null);
                else if (cmd === 'link') {
                    var url = window.prompt('Nhập URL:', 'https://');
                    if (url) document.execCommand('createLink', false, url);
                }
                else if (cmd === 'ul') document.execCommand('insertUnorderedList', false, null);
                else if (cmd === 'ol') document.execCommand('insertOrderedList', false, null);
                else if (cmd === 'blockquote') document.execCommand('formatBlock', false, 'blockquote');
                else if (cmd === 'h3') document.execCommand('formatBlock', false, 'h3');
                else if (cmd === 'code') document.execCommand('formatBlock', false, 'pre');
                else if (cmd === 'hr') document.execCommand('insertHorizontalRule', false, null);
                else if (cmd === 'table') {
                    var rows = parseInt(prompt('Số dòng:', '3'), 10) || 3;
                    var cols = parseInt(prompt('Số cột:', '3'), 10) || 3;
                    rows = Math.min(Math.max(1, rows), 20);
                    cols = Math.min(Math.max(1, cols), 10);
                    var h = '<table class="dnttvn-editor-table"><tbody>';
                    for (var r = 0; r < rows; r++) {
                        h += '<tr>';
                        for (var c = 0; c < cols; c++) h += '<td>&nbsp;</td>';
                        h += '</tr>';
                    }
                    h += '</tbody></table>';
                    document.execCommand('insertHTML', false, h);
                }
                else if (cmd === 'addrow') addRow(ed);
                else if (cmd === 'addcol') addCol(ed);
                else if (cmd === 'deleterow') deleteRow(ed);
                else if (cmd === 'deletecol') deleteCol(ed);
                else if (cmd === 'cellcolor') applyCellColorWithPalette(ed, ta, $(this));
                else if (cmd === 'textcolor') applyTextColorWithPalette(ed, ta, $(this), syncEditableToTa);
                else if (cmd === 'deletetable') deleteTable(ed);
                syncEditableToTa(ed, ta);
                if (ta) $(ta).trigger('input');
            } else if (ta) {
                var before = '', after = '';
                if (cmd === 'b') { before = '<strong>'; after = '</strong>'; }
                else if (cmd === 'i') { before = '<em>'; after = '</em>'; }
                else if (cmd === 'u') { before = '<u>'; after = '</u>'; }
                else if (cmd === 's') { before = '<del>'; after = '</del>'; }
                else if (cmd === 'link') { var url = window.prompt('Nhập URL:', 'https://'); if (url) { before = '<a href="' + url.replace(/"/g, '&quot;') + '">'; after = '</a>'; } }
                else if (cmd === 'ul') { before = '<ul><li>'; after = '</li></ul>'; }
                else if (cmd === 'ol') { before = '<ol><li>'; after = '</li></ol>'; }
                else if (cmd === 'blockquote') { before = '<blockquote>'; after = '</blockquote>'; }
                else if (cmd === 'h3') { before = '<h3>'; after = '</h3>'; }
                else if (cmd === 'code') { before = '<code>'; after = '</code>'; }
                else if (cmd === 'hr') { before = '<hr>'; after = ''; }
                else if (cmd === 'table') {
                    var rows = parseInt(prompt('Số dòng:', '3'), 10) || 3;
                    var cols = parseInt(prompt('Số cột:', '3'), 10) || 3;
                    rows = Math.min(Math.max(1, rows), 20);
                    cols = Math.min(Math.max(1, cols), 10);
                    var h = '<table class="dnttvn-editor-table"><tbody>';
                    for (var r = 0; r < rows; r++) {
                        h += '<tr>';
                        for (var c = 0; c < cols; c++) h += '<td>&nbsp;</td>';
                        h += '</tr>';
                    }
                    h += '</tbody></table>';
                    before = h;
                    after = '';
                }
                else if (cmd === 'addrow' || cmd === 'addcol' || cmd === 'deleterow' || cmd === 'deletecol' || cmd === 'cellcolor' || cmd === 'textcolor' || cmd === 'deletetable') {
                    alert('Chức năng bảng và màu (Thêm/Xóa dòng cột, Màu ô, Màu chữ, Xóa bảng) dùng khi ô nội dung đang hiển thị dạng soạn thảo. Hãy bấm vào ô nội dung rồi thử lại.');
                    before = undefined;
                    after = undefined;
                }
                if (before !== undefined || after !== undefined) {
                    var start = ta.selectionStart, end = ta.selectionEnd, val = ta.value, sel = val.substring(start, end);
                    ta.value = val.substring(0, start) + (before || '') + sel + (after || '') + val.substring(end);
                    ta.selectionStart = ta.selectionEnd = start + (before || '').length + sel.length;
                }
            }
        });

        $(document).on('submit', 'form#post', function () {
            $('.dnttvn-content-toolbar-wrap').each(function () {
                var ed = $(this).find('.dnttvn-rich-editor')[0];
                var ta = $(this).find('textarea.structured-content-item')[0];
                if (ed && ta) syncEditableToTa(ed, ta);
            });
        });
    })();

    // ===============================
    // Dynamic add Banner VVIP / VIP / Standard
    // ===============================

    // Helper to build banner item HTML for a group
    function buildBannerItemHTML(group, index) {
        var baseName = '';
        var titlePrefix = '';
        var hasTimeFields = true;

        if (group === 'vvip') {
            baseName = 'vvip';
            titlePrefix = 'Banner VVIP ';
        } else if (group === 'vip') {
            baseName = 'vip';
            titlePrefix = 'Banner VIP ';
        } else if (group === 'standard') {
            baseName = 'standard';
            titlePrefix = 'Banner Standard ';
        }

        var html = '<div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">';
        html += '<h3>' + titlePrefix + (index + 1) + '</h3>';
        html += '<p>';
        html += '<label>Hình ảnh/Video:</label><br>';
        html += '<input type="hidden" name="' + baseName + '_banners[]" class="banner-image-id" value="">';
        html += '<button type="button" class="button upload-banner-btn" data-type="' + group + '" data-index="' + index + '">Chọn hình ảnh/Video</button> ';
        html += '<button type="button" class="button remove-banner-btn" data-type="' + group + '" data-index="' + index + '">Xóa</button>';
        html += '</p>';
        html += '<div class="banner-preview" style="margin-top: 10px;"></div>';
        html += '<p>';
        html += '<label>Link (nếu có):</label><br>';
        html += '<input type="url" name="' + baseName + '_links[]" value="" class="regular-text" placeholder="https://...">';
        html += '</p>';
        html += '<p>';
        html += '<label><strong>Thời gian hiển thị:</strong></label><br>';
        html += '<input type="datetime-local" name="' + baseName + '_start[]" value="" style="max-width: 220px;">';
        html += ' đến ';
        html += '<input type="datetime-local" name="' + baseName + '_end[]" value="" style="max-width: 220px;">';
        html += '<br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>';
        html += '</p>';
        html += '<p>';
        html += '<button type="submit" name="dnttvn_save_banner_' + group + '" value="' + index + '" class="button button-secondary">Lưu ' + titlePrefix + (index + 1) + '</button>';
        html += '</p>';
        html += '</div>';

        return html;
    }

    // Add new VVIP banner row
    $('#add-vvip-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#vvip-banners-container');
        var index = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('vvip', index));
    });

    // Add new VIP banner row
    $('#add-vip-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#vip-banners-container');
        var index = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('vip', index));
    });

    // Add new Standard banner row
    $('#add-standard-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#standard-banners-container');
        var index = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('standard', index));
    });

    // ===============================
    // Live Preview for Tin tức / Cộng đồng / Doanh nghiệp
    // ===============================

    function dnttvn_update_tin_tuc_preview() {
        var title = $('#title').val() || '(Chưa có tiêu đề)';
        var content = $('#content').val() || '';
        var short = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-tin-tuc-preview-title').text(title);
        $('#dnttvn-tin-tuc-preview-excerpt').text(short);
    }

    function dnttvn_update_cong_dong_preview() {
        var title = $('#title').val() || '(Chưa có tiêu đề)';
        var content = $('#content').val() || '';
        var short = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-cong-dong-preview-title').text(title);
        $('#dnttvn-cong-dong-preview-excerpt').text(short);
    }

    function dnttvn_update_doanh_nghiep_preview() {
        var title = $('#title').val() || '(Chưa có tên doanh nghiệp)';
        // Ngành hàng / Khu vực: ưu tiên ô "Thêm mới", không có thì lấy text option đã chọn trong select
        var nganh = ($('#nganh_hang_tax_new').val() || '').trim() || ($('#nganh_hang_tax option:selected').text() || '').trim() || '';
        var khuVuc = ($('#khu_vuc_tax_new').val() || '').trim() || ($('#khu_vuc_tax option:selected').text() || '').trim() || '';
        var diaChi = $('#dia_chi').val() || '';
        var dienThoai = $('#dien_thoai').val() || '';
        var email = $('#email_lien_he').val() || '';
        var website = $('#website_doanh_nghiep').val() || '';
        var content = $('#content').val() || '';
        var shortDesc = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-doanh-nghiep-preview-title').text(title);

        var nganhText = nganh ? nganh : '(chưa nhập)';
        $('#dnttvn-doanh-nghiep-preview-nganh').html('<strong>Ngành hàng:</strong> ' + $('<div>').text(nganhText).html());

        var khuText = khuVuc ? khuVuc : '(chưa nhập)';
        $('#dnttvn-doanh-nghiep-preview-khu-vuc').html('<strong>Khu vực:</strong> ' + $('<div>').text(khuText).html());

        $('#dnttvn-doanh-nghiep-preview-dia-chi').html('<strong>Địa chỉ:</strong> ' + $('<div>').text(diaChi).html());

        var lienHeHtml = '';
        if (dienThoai) {
            lienHeHtml += '<strong>Điện thoại:</strong> ' + $('<div>').text(dienThoai).html() + '&nbsp;&nbsp;';
        }
        if (email) {
            lienHeHtml += '<strong>Email:</strong> ' + $('<div>').text(email).html() + '&nbsp;&nbsp;';
        }
        if (website) {
            lienHeHtml += '<strong>Website:</strong> ' + $('<div>').text(website).html();
        }
        $('#dnttvn-doanh-nghiep-preview-lien-he').html(lienHeHtml);
        $('#dnttvn-doanh-nghiep-preview-desc').text(shortDesc);
    }

    var $body = $('body');
    if ($body.hasClass('post-type-tin_tuc')) {
        dnttvn_update_tin_tuc_preview();
        $('#title, #content').on('input', dnttvn_update_tin_tuc_preview);
    }
    if ($body.hasClass('post-type-cong_dong')) {
        dnttvn_update_cong_dong_preview();
        $('#title, #content').on('input', dnttvn_update_cong_dong_preview);
    }
    if ($body.hasClass('post-type-doanh_nghiep')) {
        dnttvn_update_doanh_nghiep_preview();
        $('#title, #content, #dia_chi, #dien_thoai, #email_lien_he, #website_doanh_nghiep, #thong_tin_bo_sung, #nganh_hang_tax, #nganh_hang_tax_new, #khu_vuc_tax, #khu_vuc_tax_new')
            .on('input change', dnttvn_update_doanh_nghiep_preview);
    }
});
