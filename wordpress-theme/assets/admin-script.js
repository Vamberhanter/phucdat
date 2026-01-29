jQuery(document).ready(function($) {
    // Media Uploader for Hình ảnh phụ
    var mediaUploader;
    
    $('#upload_hinh_anh_phu').on('click', function(e) {
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
        
        mediaUploader.on('select', function() {
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

    $('#upload_gallery_images').on('click', function(e) {
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

        galleryUploader.on('select', function() {
            var selection   = galleryUploader.state().get('selection').toJSON();
            var ids         = [];
            var previewHtml = '';

            selection.forEach(function(attachment) {
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
    $inlineEditor.on('click', '.editinline', function() {
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
    
    // Structured Content Repeater Fields
    var structuredContentIndex = $('#structured-content-items .structured-item').length;
    
    // Add new item
    $('#add-structured-item').on('click', function(e) {
        e.preventDefault();
        
        var itemHtml = '<div class="structured-item" data-index="' + structuredContentIndex + '">' +
            '<div class="structured-item-header">' +
            '<span class="drag-handle" title="Kéo để sắp xếp">☰</span>' +
            '<strong>Mục ' + (structuredContentIndex + 1) + '</strong>' +
            '<button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>' +
            '</div>' +
            '<div class="structured-item-body">' +
            '<p>' +
            '<label><strong>Mục lớn (Tiêu đề - hiển thị in đậm):</strong></label><br>' +
            '<input type="text" name="structured_content[' + structuredContentIndex + '][heading]" ' +
            'value="" class="large-text structured-heading" placeholder="Nhập tiêu đề mục lớn...">' +
            '</p>' +
            '<p>' +
            '<label><strong>Nội dung nhỏ:</strong></label><br>' +
            '<textarea name="structured_content[' + structuredContentIndex + '][content]" ' +
            'class="large-text structured-content" rows="4" placeholder="Nhập nội dung chi tiết..."></textarea>' +
            '</p>' +
            '</div>' +
            '<div class="structured-item-preview">' +
            '<strong>Preview:</strong>' +
            '<div class="preview-content">' +
            '<strong style="font-size: 18px; color: #333;">(Chưa có tiêu đề)</strong>' +
            '<p style="margin-top: 8px; color: #666; line-height: 1.6;">(Chưa có nội dung)</p>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('#structured-content-items').append(itemHtml);
        structuredContentIndex++;
        updateStructuredContentJSON();
        updateItemNumbers();
    });
    
    // Remove item
    $(document).on('click', '.remove-item-btn', function(e) {
        e.preventDefault();
        if (confirm('Bạn có chắc muốn xóa mục này?')) {
            $(this).closest('.structured-item').remove();
            updateStructuredContentJSON();
            updateItemNumbers();
        }
    });
    
    // Update preview on input change
    $(document).on('input', '.structured-heading, .structured-content', function() {
        var $item = $(this).closest('.structured-item');
        var heading = $item.find('.structured-heading').val() || '(Chưa có tiêu đề)';
        var content = $item.find('.structured-content').val() || '(Chưa có nội dung)';
        
        var previewHtml = '<strong style="font-size: 18px; color: #333;">' + heading + '</strong>' +
            '<p style="margin-top: 8px; color: #666; line-height: 1.6;">' + 
            (content.length > 150 ? content.substring(0, 150) + '...' : content) +
            '</p>';
        
        $item.find('.preview-content').html(previewHtml);
        updateStructuredContentJSON();
    });
    
    // Update JSON hidden field
    function updateStructuredContentJSON() {
        var items = [];
        $('#structured-content-items .structured-item').each(function() {
            var heading = $(this).find('.structured-heading').val();
            var content = $(this).find('.structured-content').val();
            if (heading || content) {
                items.push({
                    heading: heading || '',
                    content: content || ''
                });
            }
        });
        $('#structured-content-json').val(JSON.stringify(items));
    }
    
    // Update item numbers
    function updateItemNumbers() {
        $('#structured-content-items .structured-item').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.structured-item-header strong').text('Mục ' + (index + 1));
        });
    }
    
    // Make items sortable (using jQuery UI sortable if available)
    if ($.fn.sortable) {
        $('#structured-content-items').sortable({
            handle: '.drag-handle',
            axis: 'y',
            update: function() {
                updateStructuredContentJSON();
                updateItemNumbers();
            }
        });
    }
    
    // Initialize JSON on page load
    updateStructuredContentJSON();

    // ===============================
    // Dynamic add Banner VVIP / VIP / Standard
    // ===============================

    // Helper to build banner item HTML for a group
    function buildBannerItemHTML(group, index) {
        var baseName      = '';
        var titlePrefix   = '';
        var hasTimeFields = true;

        if (group === 'vvip') {
            baseName    = 'vvip';
            titlePrefix = 'Banner VVIP ';
        } else if (group === 'vip') {
            baseName    = 'vip';
            titlePrefix = 'Banner VIP ';
        } else if (group === 'standard') {
            baseName    = 'standard';
            titlePrefix = 'Banner Standard ';
        }

        var html  = '<div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">';
        html     += '<h3>' + titlePrefix + (index + 1) + '</h3>';
        html     += '<p>';
        html     += '<label>Hình ảnh/Video:</label><br>';
        html     += '<input type="hidden" name="' + baseName + '_banners[]" class="banner-image-id" value="">';
        html     += '<button type="button" class="button upload-banner-btn" data-type="' + group + '" data-index="' + index + '">Chọn hình ảnh/Video</button> ';
        html     += '<button type="button" class="button remove-banner-btn" data-type="' + group + '" data-index="' + index + '">Xóa</button>';
        html     += '</p>';
        html     += '<div class="banner-preview" style="margin-top: 10px;"></div>';
        html     += '<p>';
        html     += '<label>Link (nếu có):</label><br>';
        html     += '<input type="url" name="' + baseName + '_links[]" value="" class="regular-text" placeholder="https://...">';
        html     += '</p>';
        html     += '<p>';
        html     += '<label><strong>Thời gian hiển thị:</strong></label><br>';
        html     += '<input type="datetime-local" name="' + baseName + '_start[]" value="" style="max-width: 220px;">';
        html     += ' đến ';
        html     += '<input type="datetime-local" name="' + baseName + '_end[]" value="" style="max-width: 220px;">';
        html     += '<br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>';
        html     += '</p>';
        html     += '<p>';
        html     += '<button type="submit" name="dnttvn_save_banner_' + group + '" value="' + index + '" class="button button-secondary">Lưu ' + titlePrefix + (index + 1) + '</button>';
        html     += '</p>';
        html     += '</div>';

        return html;
    }

    // Add new VVIP banner row
    $('#add-vvip-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#vvip-banners-container');
        var index      = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('vvip', index));
    });

    // Add new VIP banner row
    $('#add-vip-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#vip-banners-container');
        var index      = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('vip', index));
    });

    // Add new Standard banner row
    $('#add-standard-banner').on('click', function (e) {
        e.preventDefault();
        var $container = $('#standard-banners-container');
        var index      = $container.find('.banner-item').length;
        $container.append(buildBannerItemHTML('standard', index));
    });

    // ===============================
    // Live Preview for Tin tức / Cộng đồng / Doanh nghiệp
    // ===============================

    function dnttvn_update_tin_tuc_preview() {
        var title   = $('#title').val() || '(Chưa có tiêu đề)';
        var content = $('#content').val() || '';
        var short   = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-tin-tuc-preview-title').text(title);
        $('#dnttvn-tin-tuc-preview-excerpt').text(short);
    }

    function dnttvn_update_cong_dong_preview() {
        var title   = $('#title').val() || '(Chưa có tiêu đề)';
        var content = $('#content').val() || '';
        var short   = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-cong-dong-preview-title').text(title);
        $('#dnttvn-cong-dong-preview-excerpt').text(short);
    }

    function dnttvn_update_doanh_nghiep_preview() {
        var title     = $('#title').val() || '(Chưa có tên doanh nghiệp)';
        var nganh     = $('#nganh_hang').val() || '';
        var khuVuc    = $('#khu_vuc').val() || '';
        var diaChi    = $('#dia_chi').val() || '';
        var dienThoai = $('#dien_thoai').val() || '';
        var email     = $('#email_lien_he').val() || '';
        var website   = $('#website_doanh_nghiep').val() || '';
        var content   = $('#content').val() || '';
        var shortDesc = content.length > 250 ? content.substring(0, 250) + '…' : content;

        $('#dnttvn-doanh-nghiep-preview-title').text(title);

        var nganhText = nganh ? nganh : '(chưa nhập)';
        $('#dnttvn-doanh-nghiep-preview-nganh').find('strong').next().remove();
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
        $('#title, #content, #nganh_hang, #khu_vuc, #dia_chi, #dien_thoai, #email_lien_he, #website_doanh_nghiep, #thong_tin_bo_sung')
            .on('input change', dnttvn_update_doanh_nghiep_preview);
    }
});
