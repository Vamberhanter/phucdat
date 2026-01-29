<?php
/**
 * Functions and definitions for Cộng đồng Doanh nhân Trí tuệ Việt Nam Theme
 */

// Enqueue styles and scripts
function dnttvn_enqueue_styles() {
    wp_enqueue_style('dnttvn-main-style', get_template_directory_uri() . '/assets/style-gioi-thieu.css', array(), '1.0.0');
    wp_enqueue_script('dnttvn-main-script', get_template_directory_uri() . '/assets/script.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'dnttvn_enqueue_styles');

// Register Custom Post Type: Tin tức
function dnttvn_register_tin_tuc_post_type() {
    $labels = array(
        'name'                  => 'Tin tức',
        'singular_name'         => 'Tin tức',
        'menu_name'             => 'Tin tức',
        'name_admin_bar'        => 'Tin tức',
        'archives'              => 'Danh sách Tin tức',
        'attributes'            => 'Thuộc tính Tin tức',
        'parent_item_colon'     => 'Tin tức cha:',
        'all_items'             => 'Tất cả Tin tức',
        'add_new_item'          => 'Thêm Tin tức mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Tin tức mới',
        'edit_item'             => 'Chỉnh sửa Tin tức',
        'update_item'           => 'Cập nhật Tin tức',
        'view_item'             => 'Xem Tin tức',
        'view_items'            => 'Xem Tin tức',
        'search_items'          => 'Tìm kiếm Tin tức',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Tin tức',
        'uploaded_to_this_item' => 'Tải lên Tin tức này',
        'items_list'            => 'Danh sách Tin tức',
        'items_list_navigation' => 'Điều hướng danh sách Tin tức',
        'filter_items_list'     => 'Lọc danh sách Tin tức',
    );
    $args = array(
        'label'                 => 'Tin tức',
        'description'           => 'Quản lý tin tức của Cộng đồng',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-megaphone',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type('tin_tuc', $args);
}
add_action('init', 'dnttvn_register_tin_tuc_post_type', 0);

// Add custom meta boxes for Tin tức
function dnttvn_add_tin_tuc_meta_boxes() {
    // 1. Xem trước (ưu tiên cao nhất)
    add_meta_box(
        'tin_tuc_live_preview',
        'Xem trước Tin tức (gần giống ngoài website)',
        'dnttvn_tin_tuc_live_preview_meta_box_callback',
        'tin_tuc',
        'normal',
        'high'
    );

    // 2. Thông tin tin tức (ưu tiên trung bình)
    add_meta_box(
        'tin_tuc_details',
        'Thông tin Tin tức',
        'dnttvn_tin_tuc_meta_box_callback',
        'tin_tuc',
        'normal',
        'default'
    );

    // 3. Thêm mục với hình ảnh (ưu tiên thấp nhất)
    add_meta_box(
        'tin_tuc_structured_content',
        'Thêm mục (có thể thêm hình ảnh cho mỗi mục)',
        'dnttvn_structured_content_meta_box_callback',
        'tin_tuc',
        'normal',
        'low'
    );
    
    add_meta_box(
        'tin_tuc_author_info',
        'Thông tin Tác giả & Nguồn',
        'dnttvn_tin_tuc_author_meta_box_callback',
        'tin_tuc',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_tin_tuc_meta_boxes');

// Live preview meta box for Tin tức
function dnttvn_tin_tuc_live_preview_meta_box_callback($post) {
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-tin-tuc">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách bài Tin tức sẽ hiển thị ở trang chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card">
            <h3 id="dnttvn-tin-tuc-preview-title" class="dnttvn-preview-title" style="font-size:20px; margin:0 0 6px; color:#06202e;">
                <?php echo esc_html(get_the_title($post)); ?>
            </h3>
            <p class="dnttvn-preview-meta" style="font-size:12px; color:#999; margin:0 0 10px;">
                <strong>Ngày đăng:</strong> <span><?php echo esc_html(get_the_date('d/m/Y', $post)); ?></span>
            </p>
            <div id="dnttvn-tin-tuc-preview-excerpt" class="dnttvn-preview-excerpt" style="font-size:14px; color:#555; line-height:1.6;">
                <?php
                $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_shortcodes($post->post_content), 40, '...');
                echo esc_html($excerpt);
                ?>
            </div>
        </div>
    </div>
    <?php
}

// Meta box callback for Structured Content (Tin tức & Cộng đồng)
function dnttvn_structured_content_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_structured_content', 'dnttvn_structured_content_nonce');
    
    $structured_content = get_post_meta($post->ID, '_structured_content', true);
    $items = !empty($structured_content) ? json_decode($structured_content, true) : array();
    if (!is_array($items)) {
        $items = array();
    }
    
    ?>
    <div class="dnttvn-structured-content-wrapper">
        <p class="description" style="margin-bottom: 15px;">
            <strong>Hướng dẫn:</strong> Thêm các mục lớn (tiêu đề in đậm) và nội dung nhỏ bên dưới. Bạn có thể thêm nhiều mục và sắp xếp lại thứ tự.
        </p>
        
        <div id="structured-content-items" class="structured-content-items">
            <?php if (!empty($items)) : ?>
                <?php foreach ($items as $index => $item) : ?>
                    <div class="structured-item" data-index="<?php echo esc_attr($index); ?>">
                        <div class="structured-item-header">
                            <span class="drag-handle" title="Kéo để sắp xếp">☰</span>
                            <strong>Mục <?php echo esc_html($index + 1); ?></strong>
                            <button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>
                        </div>
                        <div class="structured-item-body">
                            <p>
                                <label><strong>Mục lớn (Tiêu đề - hiển thị in đậm):</strong></label><br>
                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][heading]"
                                       value="<?php echo esc_attr(isset($item['heading']) ? $item['heading'] : ''); ?>"
                                       class="large-text structured-heading" placeholder="Nhập tiêu đề mục lớn...">
                            </p>

                            <div class="structured-images-section">
                                <label><strong>Hình ảnh (có thể thêm nhiều hình):</strong></label>
                                <div class="images-container" id="images-container-<?php echo esc_attr($index); ?>">
                                    <?php
                                    $images = isset($item['images']) ? $item['images'] : array();
                                    $captions = isset($item['image_captions']) ? $item['image_captions'] : array();
                                    if (!is_array($images)) $images = array();
                                    if (!is_array($captions)) $captions = array();

                                    foreach ($images as $img_index => $image_id) :
                                        $caption = isset($captions[$img_index]) ? $captions[$img_index] : '';
                                        $image_url = wp_get_attachment_image_url($image_id, 'medium');
                                        ?>
                                        <div class="image-item" data-image-index="<?php echo esc_attr($img_index); ?>">
                                            <div class="image-preview">
                                                <?php if ($image_url) : ?>
                                                    <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                                <?php endif; ?>
                                                <input type="hidden" name="structured_content[<?php echo esc_attr($index); ?>][images][]" value="<?php echo esc_attr($image_id); ?>">
                                            </div>
                                            <div class="image-details">
                                                <input type="text" name="structured_content[<?php echo esc_attr($index); ?>][image_captions][]"
                                                       value="<?php echo esc_attr($caption); ?>"
                                                       placeholder="Chú thích hình ảnh..."
                                                       class="regular-text image-caption">
                                                <button type="button" class="button remove-image-btn">🗑️ Xóa</button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <p>
                                    <button type="button" class="button upload-images-btn" data-item-index="<?php echo esc_attr($index); ?>">📁 Thêm hình ảnh</button>
                                    <span class="description">Chọn nhiều hình cùng lúc. Hình đầu tiên sẽ hiển thị chính, các hình còn lại hiển thị dạng thumbnail.</span>
                                </p>
                            </div>

                            <p>
                                <label><strong>Nội dung nhỏ:</strong></label><br>
                                <textarea name="structured_content[<?php echo esc_attr($index); ?>][content]"
                                          class="large-text structured-content" rows="4"
                                          placeholder="Nhập nội dung chi tiết..."><?php echo esc_textarea(isset($item['content']) ? $item['content'] : ''); ?></textarea>
                            </p>
                        </div>
                        <div class="structured-item-preview">
                            <strong>Preview:</strong>
                            <div class="preview-content">
                                <strong style="font-size: 18px; color: #333;"><?php echo esc_html(isset($item['heading']) ? $item['heading'] : '(Chưa có tiêu đề)'); ?></strong>
                                <p style="margin-top: 8px; color: #666; line-height: 1.6;">
                                    <?php echo esc_html(isset($item['content']) ? wp_trim_words($item['content'], 30) : '(Chưa có nội dung)'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <p>
            <button type="button" id="add-structured-item" class="button button-primary">+ Thêm mục mới</button>
        </p>
        
    </div>
    
    <style>
        .dnttvn-structured-content-wrapper {
            padding: 15px;
        }
        
        .structured-content-items {
            margin-bottom: 20px;
        }
        
        .structured-item {
            background: #fff;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            margin-bottom: 15px;
            padding: 15px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .structured-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }
        
        .structured-item-header {
            background: #f7f7f7;
            padding: 10px 15px;
            margin: -15px -15px 15px -15px;
            border-bottom: 1px solid #e5e5e5;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .drag-handle {
            cursor: move;
            color: #666;
            font-size: 18px;
            user-select: none;
        }
        
        .structured-item-body {
            margin-bottom: 15px;
        }
        
        .structured-item-body label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .structured-item-body input[type="text"],
        .structured-item-body textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .structured-item-preview {
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }
        
        .structured-item-preview strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        
        .preview-content {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        
        .remove-item-btn {
            margin-left: auto;
        }
        
        #add-structured-item {
            margin-top: 10px;
        }

        /* Styles for images section */
        .structured-images-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 4px;
        }

        .images-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .image-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            min-width: 180px;
        }

        .image-preview {
            margin-bottom: 8px;
        }

        .image-preview img {
            display: block;
        }

        .image-details {
            width: 100%;
            text-align: center;
        }

        .image-caption {
            width: 100%;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .remove-image-btn {
            background: #dc3232;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }

        .remove-image-btn:hover {
            background: #c82333;
        }

        .upload-images-btn {
            margin-top: 5px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        // Upload images for structured content
        $('.upload-images-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var itemIndex = button.data('item-index');
            var container = $('#images-container-' + itemIndex);

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Chọn hình ảnh cho mục',
                button: {
                    text: 'Thêm hình ảnh đã chọn'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });

            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toArray();

                attachments.forEach(function(attachment, index) {
                    var imageUrl = attachment.attributes.sizes.medium ? attachment.attributes.sizes.medium.url : attachment.attributes.url;
                    var imageHtml = `
                        <div class="image-item" data-image-index="${index}">
                            <div class="image-preview">
                                <img src="${imageUrl}" alt="" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                <input type="hidden" name="structured_content[${itemIndex}][images][]" value="${attachment.id}">
                            </div>
                            <div class="image-details">
                                <input type="text" name="structured_content[${itemIndex}][image_captions][]"
                                       placeholder="Chú thích hình ảnh..."
                                       class="regular-text image-caption">
                                <button type="button" class="button remove-image-btn">🗑️ Xóa</button>
                            </div>
                        </div>
                    `;
                    container.append(imageHtml);
                });
            });

            mediaUploader.open();
        });

        // Remove image
        $(document).on('click', '.remove-image-btn', function() {
            $(this).closest('.image-item').remove();
        });

        // Sortable images within each container
        $(document).on('mouseenter', '.images-container', function() {
            if (!$(this).hasClass('sortable-initialized')) {
                $(this).sortable({
                    items: '.image-item',
                    cursor: 'move',
                    opacity: 0.6,
                    update: function(event, ui) {
                        // Re-index images after sorting
                        $(this).find('.image-item').each(function(index) {
                            $(this).attr('data-image-index', index);
                        });
                    }
                });
                $(this).addClass('sortable-initialized');
            }
        });

        // Existing sortable functionality for items
        $('#structured-content-items').sortable({
            items: '.structured-item',
            handle: '.drag-handle',
            cursor: 'move',
            opacity: 0.6,
            update: function(event, ui) {
                updateStructuredContentJSON();
            }
        });

        // Add new structured item
        $('#add-structured-item').on('click', function() {
            var itemCount = $('.structured-item').length;
            var newItem = `
                <div class="structured-item" data-index="${itemCount}">
                    <div class="structured-item-header">
                        <span class="drag-handle" title="Kéo để sắp xếp">☰</span>
                        <strong>Mục ${itemCount + 1}</strong>
                        <button type="button" class="button remove-item-btn" style="float: right;">Xóa mục</button>
                    </div>
                    <div class="structured-item-body">
                        <p>
                            <label><strong>Mục lớn (Tiêu đề - hiển thị in đậm):</strong></label><br>
                            <input type="text" name="structured_content[${itemCount}][heading]"
                                   class="large-text structured-heading" placeholder="Nhập tiêu đề mục lớn...">
                        </p>

                        <div class="structured-images-section">
                            <label><strong>Hình ảnh (có thể thêm nhiều hình):</strong></label>
                            <div class="images-container" id="images-container-${itemCount}"></div>
                            <p>
                                <button type="button" class="button upload-images-btn" data-item-index="${itemCount}">📁 Thêm hình ảnh</button>
                                <span class="description">Chọn nhiều hình cùng lúc. Hình đầu tiên sẽ hiển thị chính, các hình còn lại hiển thị dạng thumbnail.</span>
                            </p>
                        </div>

                        <p>
                            <label><strong>Nội dung nhỏ:</strong></label><br>
                            <textarea name="structured_content[${itemCount}][content]"
                                      class="large-text structured-content" rows="4"
                                      placeholder="Nhập nội dung chi tiết..."></textarea>
                        </p>
                    </div>
                    <div class="structured-item-preview">
                        <strong>Preview:</strong>
                        <div class="preview-content">
                            <strong style="font-size: 18px; color: #333;">(Chưa có tiêu đề)</strong>
                            <p style="margin-top: 8px; color: #666; line-height: 1.6;">(Chưa có nội dung)</p>
                        </div>
                    </div>
                </div>
            `;

            $('#structured-content-items').append(newItem);
            $('#images-container-' + itemCount).sortable({
                items: '.image-item',
                cursor: 'move',
                opacity: 0.6
            });

            // Re-bind upload button for the new item
            $('#images-container-' + itemCount).find('.upload-images-btn').on('click', function(e) {
                e.preventDefault();
                var button = $(this);
                var itemIndex = button.data('item-index');
                var container = $('#images-container-' + itemIndex);

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Chọn hình ảnh cho mục',
                    button: {
                        text: 'Thêm hình ảnh đã chọn'
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });

                mediaUploader.on('select', function() {
                    var attachments = mediaUploader.state().get('selection').toArray();

                    attachments.forEach(function(attachment, index) {
                        var imageUrl = attachment.attributes.sizes.medium ? attachment.attributes.sizes.medium.url : attachment.attributes.url;
                        var imageHtml = `
                            <div class="image-item" data-image-index="${index}">
                                <div class="image-preview">
                                    <img src="${imageUrl}" alt="" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                                    <input type="hidden" name="structured_content[${itemIndex}][images][]" value="${attachment.id}">
                                </div>
                                <div class="image-details">
                                    <input type="text" name="structured_content[${itemIndex}][image_captions][]"
                                           placeholder="Chú thích hình ảnh..."
                                           class="regular-text image-caption">
                                    <button type="button" class="button remove-image-btn">🗑️ Xóa</button>
                                </div>
                            </div>
                    `;
                    container.append(imageHtml);
                });
            });

                mediaUploader.open();
            });
        });

        // Remove structured item
        $(document).on('click', '.remove-item-btn', function() {
            $(this).closest('.structured-item').remove();
            // Re-number items
            $('.structured-item').each(function(index) {
                $(this).find('strong').first().text('Mục ' + (index + 1));
                $(this).attr('data-index', index);
            });
        });

        // Update preview on input change
        $(document).on('input', '.structured-heading, .structured-content', function() {
            var item = $(this).closest('.structured-item');
            var heading = item.find('.structured-heading').val() || '(Chưa có tiêu đề)';
            var content = item.find('.structured-content').val() || '(Chưa có nội dung)';

            item.find('.preview-content strong').text(heading);
            item.find('.preview-content p').text(content.substring(0, 100) + (content.length > 100 ? '...' : ''));
        });

        // Update preview on input change
        $(document).on('input', '.structured-heading, .structured-content', function() {
            var item = $(this).closest('.structured-item');
            var heading = item.find('.structured-heading').val() || '(Chưa có tiêu đề)';
            var content = item.find('.structured-content').val() || '(Chưa có nội dung)';

            item.find('.preview-content strong').text(heading);
            item.find('.preview-content p').text(content.substring(0, 100) + (content.length > 100 ? '...' : ''));
        });
    });
    </script>
    <?php
}

// Meta box callback for Tin tức details
function dnttvn_tin_tuc_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_tin_tuc_meta', 'dnttvn_tin_tuc_meta_nonce');
    
    $nguon = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    $tac_gia = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $ngay_dang = get_post_meta($post->ID, '_tin_tuc_ngay_dang', true);
    $thoi_gian_dang = get_post_meta($post->ID, '_tin_tuc_thoi_gian_dang', true);
    $noi_bat = get_post_meta($post->ID, '_tin_tuc_noi_bat', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="tin_tuc_tac_gia">Tác giả</label></th>
            <td>
                <input type="text" id="tin_tuc_tac_gia" name="tin_tuc_tac_gia" value="<?php echo esc_attr($tac_gia); ?>" class="regular-text" />
                <p class="description">Tên tác giả viết bài (nếu có)</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_nguon">Nguồn</label></th>
            <td>
                <input type="url" id="tin_tuc_nguon" name="tin_tuc_nguon" value="<?php echo esc_url($nguon); ?>" class="regular-text" placeholder="https://..." />
                <p class="description">Link nguồn bài viết (nếu lấy từ nơi khác)</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_thoi_gian_dang">Đăng lên lịch có giờ</label></th>
            <td>
                <input type="datetime-local" id="tin_tuc_thoi_gian_dang" name="tin_tuc_thoi_gian_dang" value="<?php echo esc_attr($thoi_gian_dang); ?>" class="regular-text" />
                <p class="description">Thời gian chính xác để đăng bài viết này. Để trống sẽ đăng ngay lập tức khi nhấn "Xuất bản".</p>
                <p class="description" style="color: #007cba;"><strong>💡 Mẹo:</strong> Bạn có thể lên lịch đăng bài vào thời điểm phù hợp để tối ưu hóa lượng người xem.</p>
            </td>
        </tr>
        <tr>
            <th><label for="tin_tuc_noi_bat">Tin nổi bật</label></th>
            <td>
                <label>
                    <input type="checkbox" id="tin_tuc_noi_bat" name="tin_tuc_noi_bat" value="1" <?php checked($noi_bat, '1'); ?> />
                    Đánh dấu tin này là tin nổi bật
                </label>
                <p class="description">Tin nổi bật sẽ được hiển thị ưu tiên trên trang chủ</p>
            </td>
        </tr>
    </table>
    <?php
}

// Meta box callback for Author info (Sidebar)
function dnttvn_tin_tuc_author_meta_box_callback($post) {
    $tac_gia = get_post_meta($post->ID, '_tin_tuc_tac_gia', true);
    $nguon = get_post_meta($post->ID, '_tin_tuc_nguon', true);
    ?>
    <div style="padding: 10px 0;">
        <p><strong>Hướng dẫn:</strong></p>
        <ul style="margin-left: 20px; margin-top: 10px;">
            <li>Nhập <strong>Tiêu đề</strong> rõ ràng, hấp dẫn</li>
            <li>Viết <strong>Nội dung</strong> đầy đủ, có format đẹp</li>
            <li>Thêm <strong>Excerpt</strong> (tóm tắt) để hiển thị ở trang chủ</li>
            <li>Chọn <strong>Featured Image</strong> (hình ảnh đại diện)</li>
            <li>Chọn <strong>Categories</strong> và <strong>Tags</strong> phù hợp</li>
        </ul>
        <?php if ($tac_gia) : ?>
            <p style="margin-top: 15px;"><strong>Tác giả:</strong> <?php echo esc_html($tac_gia); ?></p>
        <?php endif; ?>
        <?php if ($nguon) : ?>
            <p><strong>Nguồn:</strong> <a href="<?php echo esc_url($nguon); ?>" target="_blank">Xem nguồn</a></p>
        <?php endif; ?>
    </div>
    <?php
}

// Save Structured Content meta box data
function dnttvn_save_structured_content($post_id) {
    $post_types = array('tin_tuc', 'cong_dong', 'doanh_nghiep');
    
    if (!isset($_POST['dnttvn_structured_content_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_structured_content_nonce'], 'dnttvn_save_structured_content')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, $post_types)) {
        return;
    }
    
    if (isset($_POST['structured_content']) && is_array($_POST['structured_content'])) {
        $items = array();
        foreach ($_POST['structured_content'] as $item) {
            $images = isset($item['images']) ? array_map('absint', $item['images']) : array();
            $image_captions = isset($item['image_captions']) ? array_map('sanitize_text_field', $item['image_captions']) : array();

            $items[] = array(
                'heading' => sanitize_text_field(isset($item['heading']) ? $item['heading'] : ''),
                'content' => wp_kses_post(isset($item['content']) ? $item['content'] : ''),
                'images' => $images,
                'image_captions' => $image_captions
            );
        }
        update_post_meta($post_id, '_structured_content', json_encode($items, JSON_UNESCAPED_UNICODE));
    } else {
        delete_post_meta($post_id, '_structured_content');
    }
}
add_action('save_post', 'dnttvn_save_structured_content');

// Meta box callback for Cộng đồng details
function dnttvn_cong_dong_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_cong_dong_meta', 'dnttvn_cong_dong_meta_nonce');

    $mo_ta_ngan = get_post_meta($post->ID, '_cong_dong_mo_ta_ngan', true);
    $noi_bat    = get_post_meta($post->ID, '_cong_dong_noi_bat', true);
    $thoi_gian_dang = get_post_meta($post->ID, '_cong_dong_thoi_gian_dang', true);
    ?>
    <p>
        <label for="cong_dong_thoi_gian_dang"><strong>Đăng lên lịch có giờ</strong></label><br>
        <input type="datetime-local" id="cong_dong_thoi_gian_dang" name="cong_dong_thoi_gian_dang"
               value="<?php echo esc_attr($thoi_gian_dang); ?>" class="widefat" />
        <span class="description">Thời gian chính xác để đăng bài viết này. Để trống sẽ đăng ngay lập tức.</span>
        <br><span class="description" style="color: #007cba;"><strong>💡 Mẹo:</strong> Lên lịch đăng vào giờ vàng để tăng tương tác.</span>
    </p>

    <p>
        <label for="cong_dong_mo_ta_ngan"><strong>Mô tả ngắn</strong></label><br>
        <textarea id="cong_dong_mo_ta_ngan" name="cong_dong_mo_ta_ngan" rows="3" class="widefat"
                  placeholder="Mô tả ngắn sẽ hiển thị ở danh sách Cộng đồng..."><?php echo esc_textarea($mo_ta_ngan); ?></textarea>
    </p>

    <p>
        <label>
            <input type="checkbox" id="cong_dong_noi_bat" name="cong_dong_noi_bat" value="1" <?php checked($noi_bat, '1'); ?> />
            <strong>Đánh dấu bài viết này là Cộng đồng nổi bật</strong>
        </label>
    </p>
    <?php
}

// Save Cộng đồng meta box data
function dnttvn_save_cong_dong_meta($post_id) {
    if (!isset($_POST['dnttvn_cong_dong_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['dnttvn_cong_dong_meta_nonce'], 'dnttvn_save_cong_dong_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'cong_dong') {
        return;
    }

    if (isset($_POST['cong_dong_mo_ta_ngan'])) {
        update_post_meta($post_id, '_cong_dong_mo_ta_ngan', sanitize_text_field($_POST['cong_dong_mo_ta_ngan']));
    }

    // Save new datetime field
    if (isset($_POST['cong_dong_thoi_gian_dang'])) {
        $scheduled_time = sanitize_text_field($_POST['cong_dong_thoi_gian_dang']);
        update_post_meta($post_id, '_cong_dong_thoi_gian_dang', $scheduled_time);

        // Handle scheduling if time is set and in the future
        if (!empty($scheduled_time)) {
            $scheduled_timestamp = strtotime($scheduled_time);
            $current_timestamp = current_time('timestamp');

            if ($scheduled_timestamp > $current_timestamp) {
                // Schedule the post
                wp_schedule_single_event($scheduled_timestamp, 'publish_future_post', array($post_id));

                // Set post status to future if not already published
                if (get_post_status($post_id) !== 'publish') {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_status' => 'future',
                        'post_date' => date('Y-m-d H:i:s', $scheduled_timestamp),
                        'post_date_gmt' => gmdate('Y-m-d H:i:s', $scheduled_timestamp)
                    ));
                }
            }
        }
    }

    if (isset($_POST['cong_dong_noi_bat'])) {
        update_post_meta($post_id, '_cong_dong_noi_bat', '1');
    } else {
        delete_post_meta($post_id, '_cong_dong_noi_bat');
    }
}
add_action('save_post', 'dnttvn_save_cong_dong_meta');

// Save Tin tức meta box data
function dnttvn_save_tin_tuc_meta($post_id) {
    if (!isset($_POST['dnttvn_tin_tuc_meta_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_tin_tuc_meta_nonce'], 'dnttvn_save_tin_tuc_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'tin_tuc') {
        return;
    }
    
    if (isset($_POST['tin_tuc_tac_gia'])) {
        update_post_meta($post_id, '_tin_tuc_tac_gia', sanitize_text_field($_POST['tin_tuc_tac_gia']));
    }
    
    if (isset($_POST['tin_tuc_nguon'])) {
        update_post_meta($post_id, '_tin_tuc_nguon', esc_url_raw($_POST['tin_tuc_nguon']));
    }
    
    // Save new datetime field
    if (isset($_POST['tin_tuc_thoi_gian_dang'])) {
        $scheduled_time = sanitize_text_field($_POST['tin_tuc_thoi_gian_dang']);
        update_post_meta($post_id, '_tin_tuc_thoi_gian_dang', $scheduled_time);

        // Handle scheduling if time is set and in the future
        if (!empty($scheduled_time)) {
            $scheduled_timestamp = strtotime($scheduled_time);
            $current_timestamp = current_time('timestamp');

            if ($scheduled_timestamp > $current_timestamp) {
                // Schedule the post
                wp_schedule_single_event($scheduled_timestamp, 'publish_future_post', array($post_id));

                // Set post status to future if not already published
                if (get_post_status($post_id) !== 'publish') {
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_status' => 'future',
                        'post_date' => date('Y-m-d H:i:s', $scheduled_timestamp),
                        'post_date_gmt' => gmdate('Y-m-d H:i:s', $scheduled_timestamp)
                    ));
                }
            }
        }
    }
    
    if (isset($_POST['tin_tuc_noi_bat'])) {
        update_post_meta($post_id, '_tin_tuc_noi_bat', '1');
    } else {
        delete_post_meta($post_id, '_tin_tuc_noi_bat');
    }
}
add_action('save_post', 'dnttvn_save_tin_tuc_meta');

// Enhance editor for Tin tức
function dnttvn_enhance_tin_tuc_editor($settings, $editor_id) {
    if ($editor_id == 'content' && get_current_screen()->post_type == 'tin_tuc') {
        // Enable full toolbar
        $settings['toolbar1'] = 'bold,italic,underline,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv';
        $settings['toolbar2'] = 'formatselect,fontselect,fontsizeselect,forecolor,backcolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help';
        
        // Enable more features
        $settings['wordpress_adv_hidden'] = false;
        $settings['paste_as_text'] = false;
        $settings['wpautop'] = true;
        $settings['media_buttons'] = true;
    }
    return $settings;
}
add_filter('tiny_mce_before_init', 'dnttvn_enhance_tin_tuc_editor', 10, 2);

// Add admin styles for Tin tức and Cộng đồng editor
function dnttvn_tin_tuc_admin_styles() {
    global $post_type;
    if (in_array($post_type, array('tin_tuc', 'cong_dong', 'doanh_nghiep'))) {
        ?>
        <style>
            /* Improve editor area */
            #tin_tuc_details .inside,
            #cong_dong_structured_content .inside,
            #doanh_nghiep_structured_content .inside {
                padding: 15px;
            }
            
            #tin_tuc_details .form-table th {
                width: 150px;
                font-weight: 600;
            }
            
            #tin_tuc_author_info .inside {
                background: #f9f9f9;
                border-left: 4px solid #667eea;
            }
            
            /* Highlight important fields */
            .postbox#tin_tuc_details,
            .postbox#tin_tuc_structured_content,
            .postbox#cong_dong_structured_content,
            .postbox#doanh_nghiep_structured_content {
                border-left: 4px solid #667eea;
            }
            
            /* Better form styling */
            #tin_tuc_details input[type="text"],
            #tin_tuc_details input[type="url"],
            #tin_tuc_details input[type="date"] {
                width: 100%;
                max-width: 500px;
            }
            
            /* Checkbox styling */
            #tin_tuc_details input[type="checkbox"] {
                margin-right: 8px;
            }
            
            /* Structured content styles */
            .dnttvn-structured-content-wrapper {
                padding: 15px;
            }
            
            .structured-content-items {
                margin-bottom: 20px;
            }
            
            .structured-item {
                background: #fff;
                border: 2px solid #e5e5e5;
                border-radius: 6px;
                margin-bottom: 15px;
                padding: 15px;
                position: relative;
                transition: all 0.3s ease;
            }
            
            .structured-item:hover {
                border-color: #667eea;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
            }
            
            .structured-item.ui-sortable-helper {
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            }
            
            .structured-item-header {
                background: #f7f7f7;
                padding: 10px 15px;
                margin: -15px -15px 15px -15px;
                border-bottom: 1px solid #e5e5e5;
                border-radius: 6px 6px 0 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .drag-handle {
                cursor: move;
                color: #666;
                font-size: 18px;
                user-select: none;
            }
            
            .structured-item-body {
                margin-bottom: 15px;
            }
            
            .structured-item-body label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
            }
            
            .structured-item-body input[type="text"],
            .structured-item-body textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            
            .structured-item-preview {
                background: #f9f9f9;
                border: 1px solid #e5e5e5;
                border-radius: 4px;
                padding: 15px;
                margin-top: 15px;
            }
            
            .structured-item-preview strong {
                display: block;
                margin-bottom: 10px;
                color: #333;
            }
            
            .preview-content {
                background: #fff;
                padding: 15px;
                border-radius: 4px;
                border-left: 3px solid #667eea;
            }
            
            .remove-item-btn {
                margin-left: auto;
            }
            
            #add-structured-item {
                margin-top: 10px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'dnttvn_tin_tuc_admin_styles');

// Register Custom Post Type: Doanh nghiệp
function dnttvn_register_doanh_nghiep_post_type() {
    $labels = array(
        'name'                  => 'Doanh nghiệp',
        'singular_name'         => 'Doanh nghiệp',
        'menu_name'             => 'Doanh nghiệp',
        'name_admin_bar'        => 'Doanh nghiệp',
        'archives'              => 'Danh sách Doanh nghiệp',
        'attributes'            => 'Thuộc tính Doanh nghiệp',
        'parent_item_colon'     => 'Doanh nghiệp cha:',
        'all_items'             => 'Tất cả Doanh nghiệp',
        'add_new_item'          => 'Thêm Doanh nghiệp mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Doanh nghiệp mới',
        'edit_item'             => 'Chỉnh sửa Doanh nghiệp',
        'update_item'           => 'Cập nhật Doanh nghiệp',
        'view_item'             => 'Xem Doanh nghiệp',
        'view_items'            => 'Xem Doanh nghiệp',
        'search_items'          => 'Tìm kiếm Doanh nghiệp',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Doanh nghiệp',
        'uploaded_to_this_item' => 'Tải lên Doanh nghiệp này',
        'items_list'            => 'Danh sách Doanh nghiệp',
        'items_list_navigation' => 'Điều hướng danh sách Doanh nghiệp',
        'filter_items_list'     => 'Lọc danh sách Doanh nghiệp',
    );
    $args = array(
        'label'                 => 'Doanh nghiệp',
        'description'           => 'Quản lý danh sách doanh nghiệp',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('nganh_hang', 'khu_vuc'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-building',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type('doanh_nghiep', $args);
}
add_action('init', 'dnttvn_register_doanh_nghiep_post_type', 0);

// Register Custom Post Type: Cộng đồng
function dnttvn_register_cong_dong_post_type() {
    $labels = array(
        'name'                  => 'Cộng đồng',
        'singular_name'         => 'Cộng đồng',
        'menu_name'             => 'Cộng đồng',
        'name_admin_bar'        => 'Cộng đồng',
        'archives'              => 'Danh sách Cộng đồng',
        'attributes'            => 'Thuộc tính Cộng đồng',
        'parent_item_colon'     => 'Cộng đồng cha:',
        'all_items'             => 'Tất cả Cộng đồng',
        'add_new_item'          => 'Thêm Cộng đồng mới',
        'add_new'               => 'Thêm mới',
        'new_item'              => 'Cộng đồng mới',
        'edit_item'             => 'Chỉnh sửa Cộng đồng',
        'update_item'           => 'Cập nhật Cộng đồng',
        'view_item'             => 'Xem Cộng đồng',
        'view_items'            => 'Xem Cộng đồng',
        'search_items'          => 'Tìm kiếm Cộng đồng',
        'not_found'             => 'Không tìm thấy',
        'not_found_in_trash'    => 'Không tìm thấy trong thùng rác',
        'featured_image'        => 'Hình ảnh đại diện',
        'set_featured_image'    => 'Đặt hình ảnh đại diện',
        'remove_featured_image' => 'Xóa hình ảnh đại diện',
        'use_featured_image'    => 'Sử dụng làm hình ảnh đại diện',
        'insert_into_item'      => 'Chèn vào Cộng đồng',
        'uploaded_to_this_item' => 'Tải lên Cộng đồng này',
        'items_list'            => 'Danh sách Cộng đồng',
        'items_list_navigation' => 'Điều hướng danh sách Cộng đồng',
        'filter_items_list'     => 'Lọc danh sách Cộng đồng',
    );
    $args = array(
        'label'                 => 'Cộng đồng',
        'description'           => 'Quản lý bài viết Cộng đồng',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    register_post_type('cong_dong', $args);
}
add_action('init', 'dnttvn_register_cong_dong_post_type', 0);

// Add custom meta boxes for Cộng đồng
function dnttvn_add_cong_dong_meta_boxes() {
    // 1. Xem trước (ưu tiên cao nhất)
    add_meta_box(
        'cong_dong_live_preview',
        'Xem trước Cộng đồng (gần giống ngoài website)',
        'dnttvn_cong_dong_live_preview_meta_box_callback',
        'cong_dong',
        'normal',
        'high'
    );

    // 2. Thông tin cộng đồng (ưu tiên trung bình)
    add_meta_box(
        'cong_dong_details',
        'Thông tin Cộng đồng',
        'dnttvn_cong_dong_meta_box_callback',
        'cong_dong',
        'normal',
        'default'
    );

    // 3. Thêm mục với hình ảnh (ưu tiên thấp nhất)
    add_meta_box(
        'cong_dong_structured_content',
        'Thêm mục (có thể thêm hình ảnh cho mỗi mục)',
        'dnttvn_structured_content_meta_box_callback',
        'cong_dong',
        'normal',
        'low'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_cong_dong_meta_boxes');

// Live preview meta box for Cộng đồng
function dnttvn_cong_dong_live_preview_meta_box_callback($post) {
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-cong-dong">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách bài Cộng đồng sẽ hiển thị ở trang chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card">
            <h3 id="dnttvn-cong-dong-preview-title" class="dnttvn-preview-title" style="font-size:20px; margin:0 0 6px; color:#06202e;">
                <?php echo esc_html(get_the_title($post)); ?>
            </h3>
            <p class="dnttvn-preview-meta" style="font-size:12px; color:#999; margin:0 0 10px;">
                <strong>Ngày đăng:</strong> <span><?php echo esc_html(get_the_date('d/m/Y', $post)); ?></span>
            </p>
            <div id="dnttvn-cong-dong-preview-excerpt" class="dnttvn-preview-excerpt" style="font-size:14px; color:#555; line-height:1.6;">
                <?php
                $excerpt = wp_trim_words(strip_shortcodes($post->post_content), 40, '...');
                echo esc_html($excerpt);
                ?>
            </div>
        </div>
    </div>
    <?php
}

// Register Custom Taxonomies for Doanh nghiệp
function dnttvn_register_doanh_nghiep_taxonomies() {
    // Taxonomy: Ngành hàng
    register_taxonomy('nganh_hang', array('doanh_nghiep'), array(
        'hierarchical'          => true,
        'labels'                => array(
            'name'              => 'Ngành hàng',
            'singular_name'     => 'Ngành hàng',
            'search_items'      => 'Tìm kiếm Ngành hàng',
            'all_items'         => 'Tất cả Ngành hàng',
            'parent_item'       => 'Ngành hàng cha',
            'parent_item_colon' => 'Ngành hàng cha:',
            'edit_item'         => 'Chỉnh sửa Ngành hàng',
            'update_item'       => 'Cập nhật Ngành hàng',
            'add_new_item'      => 'Thêm Ngành hàng mới',
            'new_item_name'     => 'Tên Ngành hàng mới',
            'menu_name'         => 'Ngành hàng',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'nganh-hang'),
        'show_in_rest'          => true,
    ));

    // Taxonomy: Khu vực
    register_taxonomy('khu_vuc', array('doanh_nghiep'), array(
        'hierarchical'          => true,
        'labels'                => array(
            'name'              => 'Khu vực',
            'singular_name'     => 'Khu vực',
            'search_items'      => 'Tìm kiếm Khu vực',
            'all_items'         => 'Tất cả Khu vực',
            'parent_item'       => 'Khu vực cha',
            'parent_item_colon' => 'Khu vực cha:',
            'edit_item'         => 'Chỉnh sửa Khu vực',
            'update_item'       => 'Cập nhật Khu vực',
            'add_new_item'      => 'Thêm Khu vực mới',
            'new_item_name'     => 'Tên Khu vực mới',
            'menu_name'         => 'Khu vực',
        ),
        'show_ui'               => true,
        'show_admin_column'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'khu-vuc'),
        'show_in_rest'          => true,
    ));
}
add_action('init', 'dnttvn_register_doanh_nghiep_taxonomies', 0);

// Seed default Khu vực terms cho Việt Nam (theo danh sách mới, chạy 1 lần cho mỗi version)
function dnttvn_seed_vietnam_khu_vuc_terms() {
    // Dùng key version mới để vẫn chạy lại nếu trước đó đã seed danh sách cũ
    if (get_option('dnttvn_seeded_khu_vuc_terms_v2')) {
        return;
    }

    $regions = array(
        'TP. Hải Phòng',
        'TP. Đà Nẵng',
        'TP. Hồ Chí Minh',
        'TP. Cần Thơ',
        'Bắc Ninh',
        'Phú Thọ',
        'Ninh Bình',
        'Lào Cai',
        'Thái Nguyên',
        'Tuyên Quang',
        'Lâm Đồng',
        'Khánh Hòa',
        'Gia Lai',
        'Đắk Lắk',
        'Quảng Ngãi',
        'Quảng Trị',
        'Đồng Nai',
        'Tây Ninh',
        'An Giang',
        'Đồng Tháp',
        'Vĩnh Long',
        'Cà Mau',
        'Hưng Yên',
    );

    foreach ($regions as $name) {
        if (!term_exists($name, 'khu_vuc')) {
            wp_insert_term($name, 'khu_vuc');
        }
    }

    update_option('dnttvn_seeded_khu_vuc_terms_v2', 1);
}
add_action('init', 'dnttvn_seed_vietnam_khu_vuc_terms', 20);

// Add custom meta boxes for Doanh nghiệp
function dnttvn_add_doanh_nghiep_meta_boxes() {
    add_meta_box(
        'doanh_nghiep_details',
        'Thông tin Doanh nghiệp',
        'dnttvn_doanh_nghiep_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'high'
    );
    
    // Thêm meta box "Nội dung có cấu trúc" giống Tin tức/Cộng đồng
    add_meta_box(
        'doanh_nghiep_structured_content',
        'Nội dung có cấu trúc',
        'dnttvn_structured_content_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'high'
    );

    // Xem trước bố cục Doanh nghiệp
    add_meta_box(
        'doanh_nghiep_live_preview',
        'Xem trước Doanh nghiệp (gần giống ngoài website)',
        'dnttvn_doanh_nghiep_live_preview_meta_box_callback',
        'doanh_nghiep',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'dnttvn_add_doanh_nghiep_meta_boxes');

// Thêm label hướng dẫn cho phần mô tả chi tiết doanh nghiệp (trước editor)
function dnttvn_doanh_nghiep_description_label($post) {
    if (get_post_type($post) !== 'doanh_nghiep') {
        return;
    }
    ?>
    <div class="notice notice-info" style="margin: 10px 0 15px; padding: 10px 15px;">
        <p style="margin:0; font-size:13px;">
            <strong>Mô tả chi tiết doanh nghiệp:</strong> Vui lòng nhập nội dung chi tiết của doanh nghiệp ở ô soạn thảo bên dưới (nội dung này sẽ hiển thị ở phần "Mô tả chi tiết doanh nghiệp" trên trang web).
        </p>
    </div>
    <?php
}
add_action('edit_form_after_title', 'dnttvn_doanh_nghiep_description_label');

// Live preview meta box for Doanh nghiệp
function dnttvn_doanh_nghiep_live_preview_meta_box_callback($post) {
    $nganh_hang = get_post_meta($post->ID, '_nganh_hang', true);
    $khu_vuc    = get_post_meta($post->ID, '_khu_vuc', true);
    $dia_chi    = get_post_meta($post->ID, '_dia_chi', true);
    $dien_thoai = get_post_meta($post->ID, '_dien_thoai', true);
    $email_lh   = get_post_meta($post->ID, '_email_lien_he', true);
    $website_dn = get_post_meta($post->ID, '_website_doanh_nghiep', true);
    $mo_ta_ngan = get_post_meta($post->ID, '_doanh_nghiep_mo_ta_ngan', true);
    ?>
    <div class="dnttvn-live-preview-box dnttvn-live-preview-doanh-nghiep">
        <p style="margin-top:0; margin-bottom:10px; color:#555;">
            Xem nhanh cách thẻ Doanh nghiệp sẽ hiển thị ở trang danh sách/chi tiết (demo).
        </p>
        <div class="dnttvn-preview-card dnttvn-preview-business-card" style="display:flex; gap:15px;">
            <div class="dnttvn-preview-business-left" style="flex:0 0 160px;">
                <div class="dnttvn-preview-logo" style="width:100%; height:120px; background:#f0f0f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#999; font-size:12px;">
                    Logo / Hình chính
                </div>
            </div>
            <div class="dnttvn-preview-business-right" style="flex:1;">
                <h3 id="dnttvn-doanh-nghiep-preview-title" style="font-size:18px; margin:0 0 8px; color:#06202e;">
                    <?php echo esc_html(get_the_title($post)); ?>
                </h3>
                <div class="dnttvn-preview-info" style="font-size:13px; color:#555; line-height:1.6;">
                    <p id="dnttvn-doanh-nghiep-preview-nganh"><strong>Ngành hàng:</strong> <?php echo esc_html($nganh_hang); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-khu-vuc"><strong>Khu vực:</strong> <?php echo esc_html($khu_vuc); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-dia-chi"><strong>Địa chỉ:</strong> <?php echo esc_html($dia_chi); ?></p>
                    <p id="dnttvn-doanh-nghiep-preview-lien-he">
                        <?php if ($dien_thoai) : ?>
                            <strong>Điện thoại:</strong> <?php echo esc_html($dien_thoai); ?>&nbsp;&nbsp;
                        <?php endif; ?>
                        <?php if ($email_lh) : ?>
                            <strong>Email:</strong> <?php echo esc_html($email_lh); ?>&nbsp;&nbsp;
                        <?php endif; ?>
                        <?php if ($website_dn) : ?>
                            <strong>Website:</strong> <?php echo esc_html($website_dn); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div id="dnttvn-doanh-nghiep-preview-desc" style="margin-top:8px; font-size:13px; color:#666; line-height:1.6;">
                    <?php
                    if (!empty($mo_ta_ngan)) {
                        echo esc_html($mo_ta_ngan);
                    } else {
                        $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(strip_shortcodes($post->post_content), 30, '...');
                        echo esc_html($excerpt);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// Meta box callback
function dnttvn_doanh_nghiep_meta_box_callback($post) {
    wp_nonce_field('dnttvn_save_doanh_nghiep_meta', 'dnttvn_doanh_nghiep_meta_nonce');
    
    $nganh_hang    = get_post_meta($post->ID, '_nganh_hang', true);
    $khu_vuc       = get_post_meta($post->ID, '_khu_vuc', true);
    $hinh_anh_phu  = get_post_meta($post->ID, '_hinh_anh_phu', true);
    $gallery_value = get_post_meta($post->ID, '_gallery_images', true); // comma-separated IDs
    $dia_chi       = get_post_meta($post->ID, '_dia_chi', true);
    $dien_thoai    = get_post_meta($post->ID, '_dien_thoai', true);
    $email_lh      = get_post_meta($post->ID, '_email_lien_he', true);
    $website_dn    = get_post_meta($post->ID, '_website_doanh_nghiep', true);
    $thong_tin_bs  = get_post_meta($post->ID, '_thong_tin_bo_sung', true);
    $mo_ta_ngan    = get_post_meta($post->ID, '_doanh_nghiep_mo_ta_ngan', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="nganh_hang_tax">Ngành hàng</label></th>
            <td>
                <?php
                // Dropdown chọn Ngành hàng (taxonomy) đã tạo sẵn
                $nganh_terms = get_terms(array(
                    'taxonomy'   => 'nganh_hang',
                    'hide_empty' => false,
                ));
                $selected_nganh_terms = wp_get_post_terms($post->ID, 'nganh_hang', array('fields' => 'ids'));
                $selected_nganh_id    = !empty($selected_nganh_terms) ? (int) $selected_nganh_terms[0] : 0;
                ?>
                <select id="nganh_hang_tax" name="nganh_hang_tax" class="regular-text">
                    <option value=""><?php esc_html_e('— Chọn Ngành hàng đã tạo —', 'dnttvn'); ?></option>
                    <?php if (!is_wp_error($nganh_terms) && !empty($nganh_terms)) : ?>
                        <?php foreach ($nganh_terms as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($selected_nganh_id, $term->term_id); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="description">Chọn Ngành hàng đã tạo ở phần Quản lý Ngành hàng, hoặc nhập tên mới bên dưới.</p>
                <p style="margin-top: 8px;">
                    <label for="nganh_hang_tax_new"><strong>Thêm Ngành hàng mới (nếu chưa có trong danh sách):</strong></label><br>
                    <input type="text" id="nganh_hang_tax_new" name="nganh_hang_tax_new" class="regular-text" value="" placeholder="Nhập tên Ngành hàng mới..." />
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="khu_vuc_tax">Khu vực</label></th>
            <td>
                <?php
                // Dropdown chọn Khu vực (taxonomy) đã tạo sẵn
                $khu_vuc_terms = get_terms(array(
                    'taxonomy'   => 'khu_vuc',
                    'hide_empty' => false,
                ));
                $selected_khu_vuc_terms = wp_get_post_terms($post->ID, 'khu_vuc', array('fields' => 'ids'));
                $selected_khu_vuc_id    = !empty($selected_khu_vuc_terms) ? (int) $selected_khu_vuc_terms[0] : 0;
                ?>
                <select id="khu_vuc_tax" name="khu_vuc_tax" class="regular-text">
                    <option value=""><?php esc_html_e('— Chọn Khu vực đã tạo —', 'dnttvn'); ?></option>
                    <?php if (!is_wp_error($khu_vuc_terms) && !empty($khu_vuc_terms)) : ?>
                        <?php foreach ($khu_vuc_terms as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($selected_khu_vuc_id, $term->term_id); ?>>
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="description">Chọn Khu vực đã tạo ở phần Quản lý Khu vực, hoặc nhập tên mới bên dưới.</p>
                <p style="margin-top: 8px;">
                    <label for="khu_vuc_tax_new"><strong>Thêm Khu vực mới (nếu chưa có trong danh sách):</strong></label><br>
                    <input type="text" id="khu_vuc_tax_new" name="khu_vuc_tax_new" class="regular-text" value="" placeholder="Nhập tên Khu vực mới..." />
                </p>
            </td>
        </tr>
        <tr>
            <th><label for="dia_chi">Địa chỉ</label></th>
            <td>
                <input type="text" id="dia_chi" name="dia_chi" value="<?php echo esc_attr($dia_chi); ?>" class="regular-text" />
                <p class="description">Ví dụ: Số nhà, đường, quận/huyện, thành phố.</p>
            </td>
        </tr>
        <tr>
            <th><label for="dien_thoai">Điện thoại</label></th>
            <td>
                <input type="text" id="dien_thoai" name="dien_thoai" value="<?php echo esc_attr($dien_thoai); ?>" class="regular-text" />
                <p class="description">Ví dụ: 0909 000 000.</p>
            </td>
        </tr>
        <tr>
            <th><label for="email_lien_he">Email liên hệ</label></th>
            <td>
                <input type="email" id="email_lien_he" name="email_lien_he" value="<?php echo esc_attr($email_lh); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="website_doanh_nghiep">Website doanh nghiệp</label></th>
            <td>
                <input type="url" id="website_doanh_nghiep" name="website_doanh_nghiep" value="<?php echo esc_attr($website_dn); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="hinh_anh_phu">Hình ảnh phụ</label></th>
            <td>
                <input type="text" id="hinh_anh_phu" name="hinh_anh_phu" value="<?php echo esc_attr($hinh_anh_phu); ?>" class="regular-text" />
                <button type="button" class="button" id="upload_hinh_anh_phu">Chọn hình ảnh</button>
                <p class="description">
                    <strong>Lưu ý:</strong><br>
                    • <strong>Hình chính:</strong> Sử dụng "Featured Image" (Hình ảnh đại diện) ở sidebar bên phải - đây là logo/ảnh chính của doanh nghiệp<br>
                    • <strong>Hình ảnh phụ:</strong> Ảnh bổ sung hiển thị ở phần mô tả (có thể để trống). Nhập ID hoặc URL của hình ảnh, hoặc click "Chọn hình ảnh" để upload.
                </p>
                <?php if ($hinh_anh_phu) : ?>
                    <?php
                    $preview_id = is_numeric($hinh_anh_phu) ? absint($hinh_anh_phu) : attachment_url_to_postid($hinh_anh_phu);
                    if ($preview_id) {
                        echo '<div style="margin-top: 10px;">';
                        echo wp_get_attachment_image($preview_id, 'thumbnail');
                        echo '</div>';
                    } elseif (filter_var($hinh_anh_phu, FILTER_VALIDATE_URL)) {
                        echo '<div style="margin-top: 10px;"><img src="' . esc_url($hinh_anh_phu) . '" style="max-width: 150px; height: auto;" /></div>';
                    }
                    ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="gallery_images">Thư viện hình (nhiều hình)</label></th>
            <td>
                <input type="hidden" id="gallery_images" name="gallery_images" value="<?php echo esc_attr($gallery_value); ?>" />
                <button type="button" class="button" id="upload_gallery_images">Chọn nhiều hình</button>
                <p class="description">
                    Bạn có thể chọn nhiều hình để hiển thị dạng slide trong trang chi tiết Doanh nghiệp (mũi tên qua lại).
                </p>
                <div id="gallery_images_preview" style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php
                    if (!empty($gallery_value)) {
                        $ids = array_filter(array_map('absint', explode(',', $gallery_value)));
                        foreach ($ids as $img_id) {
                            $thumb = wp_get_attachment_image($img_id, 'thumbnail', false, array(
                                'style' => 'border:1px solid #ddd; padding:3px; background:#fff;'
                            ));
                            if ($thumb) {
                                echo '<div>' . $thumb . '</div>';
                            }
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="thong_tin_bo_sung">Thông tin bổ sung</label></th>
            <td>
                <textarea id="thong_tin_bo_sung" name="thong_tin_bo_sung" rows="4" class="large-text"><?php echo esc_textarea($thong_tin_bs); ?></textarea>
                <p class="description">Thêm ghi chú, thông tin chi tiết khác về doanh nghiệp (ví dụ: giờ làm việc, chính sách, người liên hệ...).</p>
            </td>
        </tr>
        <tr>
            <th><label for="doanh_nghiep_mo_ta_ngan">Mô tả ngắn (hiển thị ở thẻ doanh nghiệp)</label></th>
            <td>
                <textarea id="doanh_nghiep_mo_ta_ngan" name="doanh_nghiep_mo_ta_ngan" rows="3" class="large-text"><?php echo esc_textarea($mo_ta_ngan); ?></textarea>
                <p class="description">Mô tả ngắn gọn sẽ hiển thị ở thẻ Doanh nghiệp trên trang danh sách.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Save meta box data
function dnttvn_save_doanh_nghiep_meta($post_id) {
    if (!isset($_POST['dnttvn_doanh_nghiep_meta_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['dnttvn_doanh_nghiep_meta_nonce'], 'dnttvn_save_doanh_nghiep_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['nganh_hang'])) {
        update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_POST['nganh_hang']));
    }
    
    if (isset($_POST['khu_vuc'])) {
        update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_POST['khu_vuc']));
    }
    
    if (isset($_POST['dia_chi'])) {
        update_post_meta($post_id, '_dia_chi', sanitize_text_field($_POST['dia_chi']));
    }

    if (isset($_POST['dien_thoai'])) {
        update_post_meta($post_id, '_dien_thoai', sanitize_text_field($_POST['dien_thoai']));
    }

    if (isset($_POST['email_lien_he'])) {
        update_post_meta($post_id, '_email_lien_he', sanitize_email($_POST['email_lien_he']));
    }

    if (isset($_POST['website_doanh_nghiep'])) {
        // Lưu nguyên chuỗi người dùng nhập (không tự thêm http/https)
        update_post_meta($post_id, '_website_doanh_nghiep', sanitize_text_field($_POST['website_doanh_nghiep']));
    }
    
    if (isset($_POST['hinh_anh_phu'])) {
        update_post_meta($post_id, '_hinh_anh_phu', sanitize_text_field($_POST['hinh_anh_phu']));
    }

    if (isset($_POST['gallery_images'])) {
        $raw  = sanitize_text_field($_POST['gallery_images']);
        $ids  = array_filter(array_map('absint', explode(',', $raw)));
        $save = !empty($ids) ? implode(',', $ids) : '';
        update_post_meta($post_id, '_gallery_images', $save);
    }

    if (isset($_POST['thong_tin_bo_sung'])) {
        update_post_meta($post_id, '_thong_tin_bo_sung', wp_kses_post($_POST['thong_tin_bo_sung']));
    }

    if (isset($_POST['doanh_nghiep_mo_ta_ngan'])) {
        update_post_meta($post_id, '_doanh_nghiep_mo_ta_ngan', sanitize_text_field($_POST['doanh_nghiep_mo_ta_ngan']));
    }

    // Gán taxonomy Ngành hàng từ dropdown / ô tạo mới
    $nganh_term_id = 0;
    if (isset($_POST['nganh_hang_tax_new']) && $_POST['nganh_hang_tax_new'] !== '') {
        $term_name = sanitize_text_field($_POST['nganh_hang_tax_new']);
        $term      = wp_insert_term($term_name, 'nganh_hang');
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $nganh_term_id = (int) $term['term_id'];
        }
    } elseif (isset($_POST['nganh_hang_tax']) && $_POST['nganh_hang_tax'] !== '') {
        $nganh_term_id = (int) $_POST['nganh_hang_tax'];
    }
    if ($nganh_term_id) {
        wp_set_post_terms($post_id, array($nganh_term_id), 'nganh_hang', false);
        $term_obj = get_term($nganh_term_id, 'nganh_hang');
        if ($term_obj && !is_wp_error($term_obj)) {
            update_post_meta($post_id, '_nganh_hang', $term_obj->name);
        }
    }

    // Gán taxonomy Khu vực từ dropdown / ô tạo mới
    $khu_vuc_term_id = 0;
    if (isset($_POST['khu_vuc_tax_new']) && $_POST['khu_vuc_tax_new'] !== '') {
        $term_name = sanitize_text_field($_POST['khu_vuc_tax_new']);
        $term      = wp_insert_term($term_name, 'khu_vuc');
        if (!is_wp_error($term) && isset($term['term_id'])) {
            $khu_vuc_term_id = (int) $term['term_id'];
        }
    } elseif (isset($_POST['khu_vuc_tax']) && $_POST['khu_vuc_tax'] !== '') {
        $khu_vuc_term_id = (int) $_POST['khu_vuc_tax'];
    }
    if ($khu_vuc_term_id) {
        wp_set_post_terms($post_id, array($khu_vuc_term_id), 'khu_vuc', false);
        $term_obj = get_term($khu_vuc_term_id, 'khu_vuc');
        if ($term_obj && !is_wp_error($term_obj)) {
            update_post_meta($post_id, '_khu_vuc', $term_obj->name);
        }
    }
}
add_action('save_post', 'dnttvn_save_doanh_nghiep_meta');

// Theme support
function dnttvn_theme_setup() {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    
    // Register navigation menu
    register_nav_menus(array(
        'primary' => 'Menu chính',
    ));
}
add_action('after_setup_theme', 'dnttvn_theme_setup');

// Auto-create "Danh sách Doanh nghiệp" page on theme activation
function dnttvn_create_doanh_nghiep_page() {
    // Check if page already exists with new slug
    $page_slug = 'danh-sach-doanh-nghiep';
    $existing_page = get_page_by_path($page_slug);
    
    // Also check old slug for migration
    if (!$existing_page) {
        $old_page = get_page_by_path('page-doanh-nghiep');
        if ($old_page) {
            // Update existing page slug
            wp_update_post(array(
                'ID' => $old_page->ID,
                'post_name' => $page_slug
            ));
            $existing_page = $old_page;
        }
    }
    
    if (!$existing_page) {
        // Create the page
        $page_data = array(
            'post_title'    => 'Danh sách Doanh nghiệp',
            'post_name'     => $page_slug,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_author'   => 1,
            'page_template' => 'page-doanh-nghiep.php',
        );
        
        $page_id = wp_insert_post($page_data);
        
        // Set page template
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-doanh-nghiep.php');
        }
    } else {
        // Update existing page to use correct template and slug
        wp_update_post(array(
            'ID' => $existing_page->ID,
            'post_name' => $page_slug
        ));
        update_post_meta($existing_page->ID, '_wp_page_template', 'page-doanh-nghiep.php');
    }
}
add_action('after_switch_theme', 'dnttvn_create_doanh_nghiep_page');

// Default menu fallback
function dnttvn_default_menu() {
    echo '<ul class="menu" id="mainMenu">';
    echo '<li><a href="' . esc_url(home_url()) . '">Trang chủ</a></li>';
    $tin_tuc_link = get_post_type_archive_link('tin_tuc');
    if ($tin_tuc_link) {
        echo '<li><a href="' . esc_url($tin_tuc_link) . '">Tin tức</a></li>';
    }
    $page_doanh_nghiep = get_page_by_path('danh-sach-doanh-nghiep');
    if (!$page_doanh_nghiep) {
        // Fallback to old slug
        $page_doanh_nghiep = get_page_by_path('page-doanh-nghiep');
    }
    if ($page_doanh_nghiep) {
        echo '<li><a href="' . esc_url(get_permalink($page_doanh_nghiep->ID)) . '">Danh sách Doanh nghiệp</a></li>';
    }
    echo '<li><a href="#">Giới thiệu</a></li>';
    echo '<li><a href="#">Liên hệ</a></li>';
    echo '</ul>';
}

// ============================================
// ADMIN MANAGEMENT FEATURES
// ============================================

// Add Admin Columns for Doanh nghiệp
function dnttvn_add_doanh_nghiep_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['menu_order'] = 'Thứ tự';
    $new_columns['nganh_hang'] = 'Ngành hàng';
    $new_columns['khu_vuc'] = 'Khu vực';
    $new_columns['nganh_hang_tax'] = 'Ngành hàng (phân loại)';
    $new_columns['khu_vuc_tax'] = 'Khu vực (phân loại)';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_doanh_nghiep_posts_columns', 'dnttvn_add_doanh_nghiep_admin_columns');

// Populate Admin Columns for Doanh nghiệp
function dnttvn_populate_doanh_nghiep_admin_columns($column, $post_id) {
    switch ($column) {
        case 'menu_order':
            $menu_order = get_post($post_id)->menu_order;
            echo '<strong>' . esc_html($menu_order) . '</strong>';
            break;
        case 'nganh_hang':
            $nganh_hang = get_post_meta($post_id, '_nganh_hang', true);
            echo $nganh_hang ? esc_html($nganh_hang) : '—';
            break;
        case 'khu_vuc':
            $khu_vuc = get_post_meta($post_id, '_khu_vuc', true);
            echo $khu_vuc ? esc_html($khu_vuc) : '—';
            break;
        case 'nganh_hang_tax':
            $terms = get_the_terms($post_id, 'nganh_hang');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo implode(', ', $term_names);
            } else {
                echo '—';
            }
            break;
        case 'khu_vuc_tax':
            $terms = get_the_terms($post_id, 'khu_vuc');
            if ($terms && !is_wp_error($terms)) {
                $term_names = array();
                foreach ($terms as $term) {
                    $term_names[] = $term->name;
                }
                echo implode(', ', $term_names);
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_doanh_nghiep_posts_custom_column', 'dnttvn_populate_doanh_nghiep_admin_columns', 10, 2);

// Make Admin Columns Sortable
function dnttvn_make_doanh_nghiep_columns_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    $columns['nganh_hang'] = 'nganh_hang';
    $columns['khu_vuc'] = 'khu_vuc';
    return $columns;
}
add_filter('manage_edit-doanh_nghiep_sortable_columns', 'dnttvn_make_doanh_nghiep_columns_sortable');

// Handle Sorting
function dnttvn_handle_doanh_nghiep_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby == 'menu_order') {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    } elseif ($orderby == 'nganh_hang' || $orderby == 'khu_vuc') {
        $query->set('meta_key', '_' . $orderby);
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'dnttvn_handle_doanh_nghiep_sorting');

// Add Admin Columns for Tin tức
function dnttvn_add_tin_tuc_admin_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['menu_order'] = 'Thứ tự';
    $new_columns['noi_bat'] = 'Nổi bật';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_tin_tuc_posts_columns', 'dnttvn_add_tin_tuc_admin_columns');

// Populate Admin Columns for Tin tức
function dnttvn_populate_tin_tuc_admin_columns($column, $post_id) {
    switch ($column) {
        case 'menu_order':
            $menu_order = get_post($post_id)->menu_order;
            echo '<strong>' . esc_html($menu_order) . '</strong>';
            break;
        case 'noi_bat':
            $noi_bat = get_post_meta($post_id, '_tin_tuc_noi_bat', true);
            if ($noi_bat == '1') {
                echo '<span style="color: #ff9800; font-weight: bold;">⭐ Nổi bật</span>';
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_tin_tuc_posts_custom_column', 'dnttvn_populate_tin_tuc_admin_columns', 10, 2);

// Make Tin tức Columns Sortable
function dnttvn_make_tin_tuc_columns_sortable($columns) {
    $columns['menu_order'] = 'menu_order';
    $columns['noi_bat'] = 'noi_bat';
    return $columns;
}
add_filter('manage_edit-tin_tuc_sortable_columns', 'dnttvn_make_tin_tuc_columns_sortable');

// Handle Tin tức Sorting
function dnttvn_handle_tin_tuc_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $orderby = $query->get('orderby');
    if ($orderby == 'menu_order') {
        $query->set('orderby', 'menu_order');
        $query->set('order', 'ASC');
    } elseif ($orderby == 'noi_bat') {
        $query->set('meta_key', '_tin_tuc_noi_bat');
        $query->set('orderby', 'meta_value date');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'dnttvn_handle_tin_tuc_sorting');

// Add Admin Filters (Dropdown filters)
function dnttvn_add_doanh_nghiep_admin_filters() {
    global $typenow;
    
    if ($typenow == 'doanh_nghiep') {
        // Filter by Ngành hàng taxonomy
        $selected_nganh_hang = isset($_GET['nganh_hang_filter']) ? $_GET['nganh_hang_filter'] : '';
        $nganh_hang_terms = get_terms(array(
            'taxonomy' => 'nganh_hang',
            'hide_empty' => false,
        ));
        
        if (!empty($nganh_hang_terms) && !is_wp_error($nganh_hang_terms)) {
            echo '<select name="nganh_hang_filter">';
            echo '<option value="">Tất cả Ngành hàng</option>';
            foreach ($nganh_hang_terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_nganh_hang, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
        
        // Filter by Khu vực taxonomy
        $selected_khu_vuc = isset($_GET['khu_vuc_filter']) ? $_GET['khu_vuc_filter'] : '';
        $khu_vuc_terms = get_terms(array(
            'taxonomy' => 'khu_vuc',
            'hide_empty' => false,
        ));
        
        if (!empty($khu_vuc_terms) && !is_wp_error($khu_vuc_terms)) {
            echo '<select name="khu_vuc_filter">';
            echo '<option value="">Tất cả Khu vực</option>';
            foreach ($khu_vuc_terms as $term) {
                echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected_khu_vuc, $term->slug, false) . '>' . esc_html($term->name) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action('restrict_manage_posts', 'dnttvn_add_doanh_nghiep_admin_filters');

// Apply Admin Filters
function dnttvn_apply_doanh_nghiep_admin_filters($query) {
    global $pagenow, $typenow;
    
    if ($pagenow == 'edit.php' && $typenow == 'doanh_nghiep') {
        $tax_query = array();
        
        if (isset($_GET['nganh_hang_filter']) && $_GET['nganh_hang_filter'] != '') {
            $tax_query[] = array(
                'taxonomy' => 'nganh_hang',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['nganh_hang_filter']),
            );
        }
        
        if (isset($_GET['khu_vuc_filter']) && $_GET['khu_vuc_filter'] != '') {
            $tax_query[] = array(
                'taxonomy' => 'khu_vuc',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['khu_vuc_filter']),
            );
        }
        
        if (!empty($tax_query)) {
            $query->set('tax_query', $tax_query);
        }
    }
}
add_action('parse_query', 'dnttvn_apply_doanh_nghiep_admin_filters');

// Add Quick Edit Fields
function dnttvn_add_quick_edit_fields($column_name, $post_type) {
    if ($post_type != 'doanh_nghiep') {
        return;
    }
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title">Ngành hàng</span>
                    <input type="text" name="nganh_hang" value="" />
                </label>
            </div>
            <div class="inline-edit-group wp-clearfix">
                <label class="inline-edit-status alignleft">
                    <span class="title">Khu vực</span>
                    <input type="text" name="khu_vuc" value="" />
                </label>
            </div>
        </div>
    </fieldset>
    <?php
}
add_action('quick_edit_custom_box', 'dnttvn_add_quick_edit_fields', 10, 2);

// Save Quick Edit Data
function dnttvn_save_quick_edit_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'doanh_nghiep') {
        return;
    }
    
    if (isset($_POST['nganh_hang'])) {
        update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_POST['nganh_hang']));
    }
    
    if (isset($_POST['khu_vuc'])) {
        update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_POST['khu_vuc']));
    }
}
add_action('save_post', 'dnttvn_save_quick_edit_data');

// Add Bulk Edit Fields
function dnttvn_add_bulk_edit_fields() {
    global $post_type;
    
    if ($post_type == 'doanh_nghiep') {
        ?>
        <div class="inline-edit-group">
            <label class="alignleft">
                <span class="title">Ngành hàng</span>
                <input type="text" name="_nganh_hang" value="" />
            </label>
        </div>
        <div class="inline-edit-group">
            <label class="alignleft">
                <span class="title">Khu vực</span>
                <input type="text" name="_khu_vuc" value="" />
            </label>
        </div>
        <?php
    }
}
add_action('bulk_edit_custom_box', 'dnttvn_add_bulk_edit_fields', 10, 2);

// Save Bulk Edit Data
function dnttvn_save_bulk_edit_data($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (get_post_type($post_id) != 'doanh_nghiep') {
        return;
    }
    
    // Check if this is bulk edit
    if (isset($_REQUEST['_inline_edit']) || isset($_REQUEST['bulk_edit'])) {
        if (isset($_REQUEST['_nganh_hang']) && $_REQUEST['_nganh_hang'] != '') {
            update_post_meta($post_id, '_nganh_hang', sanitize_text_field($_REQUEST['_nganh_hang']));
        }
        
        if (isset($_REQUEST['_khu_vuc']) && $_REQUEST['_khu_vuc'] != '') {
            update_post_meta($post_id, '_khu_vuc', sanitize_text_field($_REQUEST['_khu_vuc']));
        }
    }
}
add_action('save_post', 'dnttvn_save_bulk_edit_data');

// Enqueue Admin Scripts for Media Uploader & Structured Content
function dnttvn_enqueue_admin_scripts($hook) {
    global $post_type;
    
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        // Doanh nghiệp: cần media uploader + script quản lý hình ảnh / gallery + Structured Content
        if ($post_type == 'doanh_nghiep') {
            wp_enqueue_media();
            wp_enqueue_script(
                'dnttvn-admin-script',
                get_template_directory_uri() . '/assets/admin-script.js',
                array('jquery'),
                '1.0.0',
                true
            );
        }

        // Tin tức & Cộng đồng: dùng chung script để hỗ trợ Structured Content (repeater)
        if (in_array($post_type, array('tin_tuc', 'cong_dong'))) {
            wp_enqueue_script(
                'dnttvn-admin-script',
                get_template_directory_uri() . '/assets/admin-script.js',
                array('jquery'),
                '1.0.0',
                true
            );

            // Tin tức vẫn cần media buttons cho editor
            if ($post_type === 'tin_tuc') {
                wp_enqueue_media();
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_admin_scripts');

// Add Dashboard Widgets
function dnttvn_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'dnttvn_stats_widget',
        'Thống kê Cộng đồng',
        'dnttvn_dashboard_stats_widget'
    );
}
add_action('wp_dashboard_setup', 'dnttvn_add_dashboard_widgets');

// ============================================
// SEO & META HELPERS
// ============================================

// Lấy meta description cho trang hiện tại
function dnttvn_get_meta_description() {
    if (is_singular()) {
        global $post;
        if (!$post) {
            return get_bloginfo('description');
        }

        // Ưu tiên Excerpt
        if (has_excerpt($post)) {
            return wp_strip_all_tags(get_the_excerpt($post), true);
        }

        // Với Tin tức / Doanh nghiệp / Cộng đồng, cắt từ nội dung
        $content = $post->post_content;
        if (!empty($content)) {
            $text = wp_strip_all_tags(strip_shortcodes($content), true);
            return mb_substr($text, 0, 155);
        }
    }

    // Trang khác: dùng mô tả site
    $desc = get_bloginfo('description');
    return $desc ? $desc : get_bloginfo('name');
}

// Lấy OG image cho chia sẻ mạng xã hội
function dnttvn_get_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        if (!empty($img[0])) {
            return $img[0];
        }
    }

    // Fallback: logo custom
    if (function_exists('get_custom_logo')) {
        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $img = wp_get_attachment_image_src($logo_id, 'full');
            if (!empty($img[0])) {
                return $img[0];
            }
        }
    }

    // Fallback cuối: không trả về gì
    return '';
}

// Dashboard Stats Widget
function dnttvn_dashboard_stats_widget() {
    $tin_tuc_count = wp_count_posts('tin_tuc');
    $doanh_nghiep_count = wp_count_posts('doanh_nghiep');
    
    $nganh_hang_count = wp_count_terms(array('taxonomy' => 'nganh_hang', 'hide_empty' => false));
    $khu_vuc_count = wp_count_terms(array('taxonomy' => 'khu_vuc', 'hide_empty' => false));
    
    if (is_wp_error($nganh_hang_count)) {
        $nganh_hang_count = 0;
    }
    if (is_wp_error($khu_vuc_count)) {
        $khu_vuc_count = 0;
    }
    
    ?>
    <div style="padding: 10px;">
        <h3>Tin tức</h3>
        <ul>
            <li>Đã xuất bản: <strong><?php echo number_format_i18n($tin_tuc_count->publish); ?></strong></li>
            <li>Bản nháp: <strong><?php echo number_format_i18n($tin_tuc_count->draft); ?></strong></li>
            <li>Trong thùng rác: <strong><?php echo number_format_i18n($tin_tuc_count->trash); ?></strong></li>
        </ul>
        
        <h3>Doanh nghiệp</h3>
        <ul>
            <li>Đã xuất bản: <strong><?php echo number_format_i18n($doanh_nghiep_count->publish); ?></strong></li>
            <li>Bản nháp: <strong><?php echo number_format_i18n($doanh_nghiep_count->draft); ?></strong></li>
            <li>Trong thùng rác: <strong><?php echo number_format_i18n($doanh_nghiep_count->trash); ?></strong></li>
        </ul>
        
        <h3>Phân loại</h3>
        <ul>
            <li>Ngành hàng: <strong><?php echo number_format_i18n($nganh_hang_count); ?></strong></li>
            <li>Khu vực: <strong><?php echo number_format_i18n($khu_vuc_count); ?></strong></li>
        </ul>
        
        <p style="margin-top: 15px;">
            <a href="<?php echo admin_url('edit.php?post_type=tin_tuc'); ?>" class="button">Quản lý Tin tức</a>
            <a href="<?php echo admin_url('edit.php?post_type=doanh_nghiep'); ?>" class="button">Quản lý Doanh nghiệp</a>
        </p>
    </div>
    <?php
}

// Custom Pagination Function for Doanh nghiệp page
function dnttvn_custom_pagination($query = null) {
    global $wp_query;
    
    if (!$query) {
        $query = $wp_query;
    }
    
    $big = 999999999; // Need an unlikely integer
    $total_pages = $query->max_num_pages;
    
    if ($total_pages <= 1) {
        return '';
    }
    
    // Get current page
    // For custom page templates, check multiple sources
    $current_page = 1;
    if (get_query_var('paged')) {
        $current_page = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $current_page = get_query_var('page');
    } elseif (isset($_GET['paged']) && is_numeric($_GET['paged'])) {
        $current_page = absint($_GET['paged']);
    }
    $current_page = max(1, $current_page);
    
    // Build query string to preserve search, filter, and sort parameters
    $query_args = array();
    if (isset($_GET['ten_doanh_nghiep']) && !empty($_GET['ten_doanh_nghiep'])) {
        $query_args['ten_doanh_nghiep'] = sanitize_text_field($_GET['ten_doanh_nghiep']);
    }
    if (isset($_GET['khu_vuc']) && !empty($_GET['khu_vuc'])) {
        $query_args['khu_vuc'] = sanitize_text_field($_GET['khu_vuc']);
    }
    if (isset($_GET['nganh_hang']) && !empty($_GET['nganh_hang'])) {
        $query_args['nganh_hang'] = sanitize_text_field($_GET['nganh_hang']);
    }
    if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
        $query_args['sort_by'] = sanitize_text_field($_GET['sort_by']);
    }
    
    // For custom page templates, use get_permalink() as base
    global $post;
    $page_permalink = '';
    if (is_page() && isset($post)) {
        $page_permalink = get_permalink($post->ID);
    } else {
        // Fallback to get_pagenum_link for archive pages
        $page_permalink = get_pagenum_link($big);
    }
    
    // Build base URL for pagination (query string ?paged=)
    $base = add_query_arg(array_merge($query_args, array('paged' => '%#%')), $page_permalink);
    
    $pagination = paginate_links(array(
        'base'      => $base,
        'format'    => '',
        'current'   => $current_page,
        'total'     => $total_pages,
        'prev_text' => '&laquo; Trước',
        'next_text' => 'Sau &raquo;',
        'type'      => 'list',
        'end_size'  => 1,
        'mid_size'  => 2, // cửa sổ 5 trang quanh trang hiện tại
    ));
    
    return '<div class="pagination">' . $pagination . '</div>';
}

// Helper function to render banner blocks (for reuse in sidebar and mobile)
// $offset, $limit dùng để phục vụ view mobile (lấy theo chỉ số để xen kẽ với thẻ doanh nghiệp)
function dnttvn_render_banner_blocks($class_prefix = 'ad-block', $offset = 0, $limit = null) {
    $now   = current_time('timestamp');
    $index = 0; // Đếm số banner thực sự hiển thị (sau khi lọc thời gian)
    // Get banner order
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    $order_array = array_map('trim', explode(',', $banner_column_order));
    
    // Prepare banner data
    $banner_data = array(
        'vvip' => array(
            'banners' => get_option('dnttvn_vvip_banners', array()),
            'links' => get_option('dnttvn_vvip_links', array()),
            'type' => 'vvip',
            'title' => 'Video quảng cáo hoặc banner: VVIP',
            'label' => 'VVIP'
        ),
        'vip' => array(
            'banners' => get_option('dnttvn_vip_banners', array()),
            'links' => get_option('dnttvn_vip_links', array()),
            'type' => 'vip',
            'title' => 'Video quảng cáo hoặc banner: VIP',
            'label' => 'VIP'
        ),
        'standard' => array(
            'banners' => get_option('dnttvn_standard_banners', array()),
            'links' => get_option('dnttvn_standard_links', array()),
            'type' => 'standard',
            'title' => 'Video quảng cáo hoặc banner: Standard',
            'label' => 'Standard'
        )
    );
    
    $output = '';
    
    // Display banners according to order
    foreach ($order_array as $banner_type) {
        if (!isset($banner_data[$banner_type])) continue;
        
        $data    = $banner_data[$banner_type];
        $banners = $data['banners'];
        $links   = $data['links'];

        // Lấy thời gian hiển thị tương ứng với từng loại banner
        $start_times = get_option('dnttvn_' . $banner_type . '_start', array());
        $end_times   = get_option('dnttvn_' . $banner_type . '_end', array());
        
        if (!empty($banners)) {
            foreach ($banners as $idx => $banner_id) {
                if ($banner_id) {
                    // Kiểm tra thời gian hiển thị (nếu có cấu hình)
                    $start_raw = isset($start_times[$idx]) ? $start_times[$idx] : '';
                    $end_raw   = isset($end_times[$idx]) ? $end_times[$idx] : '';
                    $start_ts  = $start_raw ? strtotime($start_raw) : false;
                    $end_ts    = $end_raw ? strtotime($end_raw) : false;

                    if (($start_ts && $now < $start_ts) || ($end_ts && $now > $end_ts)) {
                        continue;
                    }

                    // Lấy đúng URL theo loại file (image/video)
                    $mime_type = get_post_mime_type($banner_id);
                    if (strpos($mime_type, 'video') !== false) {
                        // Video: dùng URL gốc của file video
                        $banner_url = wp_get_attachment_url($banner_id);
                        $banner_alt = ''; // video không dùng alt
                    } else {
                        // Ảnh: dùng URL ảnh theo kích thước full
                        $banner_url = wp_get_attachment_image_url($banner_id, 'full');
                        $banner_alt = get_post_meta($banner_id, '_wp_attachment_image_alt', true);
                    }

                    $link_url = isset($links[$index]) ? $links[$index] : '';
                    if ($banner_url) {
                        // Áp dụng offset/limit nếu có (phục vụ view mobile)
                        if ($index < $offset) {
                            $index++;
                            continue;
                        }
                        if ($limit !== null && $index >= $offset + $limit) {
                            // Đã đủ số lượng cần lấy
                            return $output;
                        }

                        $output .= '<div class="' . esc_attr($class_prefix) . ' ' . esc_attr($data['type']) . '">';
                        $output .= '<h4>' . esc_html($data['title']) . '</h4>';
                        $output .= '<div class="ad-type">' . esc_html($data['label']) . '</div>';
                        $output .= '<div class="ad-description">Blog 15 giây, tối đa 5 doanh nghiệp</div>';
                        if ($link_url) {
                            $output .= '<a href="' . esc_url($link_url) . '" target="_blank">';
                        }
                        // Hiển thị video hoặc ảnh tùy theo mime type
            $mime_type = get_post_mime_type($banner_id);
            if (strpos($mime_type, 'video') !== false) {
                // Tự động chạy, lặp lại, không hiển thị thanh điều khiển
                $output .= '<video src="' . esc_url($banner_url) . '" autoplay muted loop playsinline style="width: 100%; max-width: 100%;"></video>';
            } else {
                $output .= '<img src="' . esc_url($banner_url) . '" alt="' . esc_attr($banner_alt) . '" style="width: 100%; max-width: 100%;">';
            }
                        if ($link_url) {
                            $output .= '</a>';
                        }
                        $output .= '</div>';
                        $index++;
                    }
                }
            }
        }
    }
    
    return $output;
}

// Add Custom Fields to Search
function dnttvn_extend_admin_search($search, $wp_query) {
    global $wpdb;
    
    if (!is_admin() || !$wp_query->is_search() || !isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'doanh_nghiep') {
        return $search;
    }
    
    $search_term = $wp_query->query_vars['s'];
    $search = " AND (
        {$wpdb->posts}.post_title LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        OR {$wpdb->posts}.post_content LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        OR EXISTS (
            SELECT 1 FROM {$wpdb->postmeta}
            WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
            AND (
                {$wpdb->postmeta}.meta_key = '_nganh_hang'
                OR {$wpdb->postmeta}.meta_key = '_khu_vuc'
            )
            AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql($wpdb->esc_like($search_term)) . "%'
        )
    )";
    
    return $search;
}
add_filter('posts_search', 'dnttvn_extend_admin_search', 10, 2);

// ============================================
// BANNER MANAGEMENT
// ============================================

// Register Customizer for Header Banners
function dnttvn_customize_register($wp_customize) {
    // Add Section for Header Banners
    $wp_customize->add_section('dnttvn_header_banners', array(
        'title'    => 'Banner Header',
        'priority' => 30,
    ));
    
    // Banner 1
    $wp_customize->add_setting('dnttvn_banner_1', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_1', array(
        'label'     => 'Banner 1',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 2
    $wp_customize->add_setting('dnttvn_banner_2', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_2', array(
        'label'     => 'Banner 2',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 3
    $wp_customize->add_setting('dnttvn_banner_3', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_3', array(
        'label'     => 'Banner 3',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 4
    $wp_customize->add_setting('dnttvn_banner_4', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_4', array(
        'label'     => 'Banner 4',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner 5
    $wp_customize->add_setting('dnttvn_banner_5', array(
        'default'           => '',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dnttvn_banner_5', array(
        'label'     => 'Banner 5',
        'section'   => 'dnttvn_header_banners',
        // Cho phép chọn cả hình ảnh và video
    )));
    
    // Banner Order (Thứ tự hiển thị)
    $wp_customize->add_setting('dnttvn_banner_order', array(
        'default'           => '1,2,3,4,5',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('dnttvn_banner_order', array(
        'label'       => 'Thứ tự hiển thị Banner',
        'section'     => 'dnttvn_header_banners',
        'type'        => 'text',
        'description' => 'Nhập thứ tự banner (ví dụ: 1,2,3,4,5 hoặc 5,4,3,2,1). Phân cách bằng dấu phẩy.',
    ));

    // ================================
    // Social Links (Header)
    // ================================
    $wp_customize->add_section('dnttvn_social_links', array(
        'title'       => 'Liên kết mạng xã hội (Header)',
        'priority'    => 40,
        'description' => 'Cấu hình link Facebook, TikTok, Zalo, YouTube hiển thị ở phần KÊNH LIÊN KẾT trên header.',
    ));

    $social_networks = array(
        'facebook' => 'Facebook',
        'tiktok'   => 'TikTok',
        'zalo'     => 'Zalo',
        'youtube'  => 'YouTube',
    );

    foreach ($social_networks as $key => $label) {
        $setting_id = 'dnttvn_social_' . $key . '_url';
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $label . ' URL',
            'section'     => 'dnttvn_social_links',
            'type'        => 'url',
            'input_attrs' => array(
                'placeholder' => 'https://...',
            ),
        ));
    }

    // ================================
    // Sidebar Links (Website liên kết)
    // ================================
    // Community links now managed via admin page (Quản lý Link Cộng đồng)
    // Old Customizer section commented out for reference
    /*
    $wp_customize->add_section('dnttvn_sidebar_links', array(
        'title'       => 'Liên kết Website (Sidebar)',
        'priority'    => 50,
        'description' => 'Cấu hình link cho các mục "Cộng đồng" ở cột phải.',
    ));

    $sidebar_links = array(
        'cong_dong_main' => 'Trang Cộng đồng',
        'cong_dong_tre'  => 'Cộng đồng Doanh nhân Trẻ',
        'khoi_nghiep'    => 'Cộng đồng Khởi nghiệp',
        'dau_tu'         => 'Cộng đồng Đầu tư',
    );

    foreach ($sidebar_links as $key => $label) {
        $setting_id = 'dnttvn_sidebar_link_' . $key;
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'       => $label . ' – URL',
            'section'     => 'dnttvn_sidebar_links',
            'type'        => 'url',
            'input_attrs' => array(
                'placeholder' => 'https://...',
            ),
        ));
    }
    */
}
add_action('customize_register', 'dnttvn_customize_register');

// Header Banner Settings Page
function dnttvn_header_banner_settings_page() {
    // Xử lý lưu dữ liệu header banner
    if (isset($_POST['dnttvn_save_banner_header']) && check_admin_referer('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce')) {
        if (isset($_POST['header_banners']) && is_array($_POST['header_banners'])) {
            // Lưu vào theme_mod cũ để tương thích (1–5)
            for ($i = 1; $i <= 5; $i++) {
                $banner_id = isset($_POST['header_banners'][$i]) ? absint($_POST['header_banners'][$i]) : 0;
                set_theme_mod('dnttvn_banner_' . $i, $banner_id);
            }
        }

        // Lưu thời gian hiển thị cho từng Banner Header
        $header_start = isset($_POST['header_start']) && is_array($_POST['header_start'])
            ? array_map('sanitize_text_field', $_POST['header_start'])
            : array();
        $header_end   = isset($_POST['header_end']) && is_array($_POST['header_end'])
            ? array_map('sanitize_text_field', $_POST['header_end'])
            : array();
        update_option('dnttvn_header_start', $header_start);
        update_option('dnttvn_header_end', $header_end);

        if (isset($_POST['header_banner_order'])) {
            set_theme_mod('dnttvn_banner_order', sanitize_text_field($_POST['header_banner_order']));
        }

        echo '<div class="notice notice-success"><p>Đã lưu banner header thành công!</p></div>';
    }

    // Header banners (for header slider)
    $header_banners = array();
    for ($i = 1; $i <= 5; $i++) {
        $header_banners[$i] = get_theme_mod('dnttvn_banner_' . $i, '');
    }
    $header_banner_order = get_theme_mod('dnttvn_banner_order', '1,2,3,4,5');
    $header_start        = get_option('dnttvn_header_start', array());
    $header_end          = get_option('dnttvn_header_end', array());

    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner Header</h1>
        <p class="description">Quản lý banner hiển thị trong slider ở phần đầu trang (Header). Hỗ trợ cả hình ảnh và video.</p>

        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_header_banners_action', 'dnttvn_save_header_banners_nonce'); ?>

            <h2>Banner Header (Slider trên đầu trang)</h2>
            <p class="description">Chọn tối đa 5 banner để hiển thị trong slider ở phần đầu trang (Header). Thứ tự hiển thị có thể thay đổi bên dưới.</p>
            <div id="header-banners-container">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id    = isset($header_banners[$i]) ? $header_banners[$i] : '';
                    $start_value  = isset($header_start[$i]) ? $header_start[$i] : '';
                    $end_value    = isset($header_end[$i]) ? $header_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Header <?php echo $i; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Banner Header <?php echo $i; ?>:</strong></label><br>
                            <input type="hidden" name="header_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="header" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="header" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước ngang, định dạng JPG/PNG/MP4.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php
                            if ($banner_id) {
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            }
                            ?>
                        </div>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="header_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="header_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_header" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Header <?php echo $i; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>

            <h3>Thứ tự hiển thị Banner Header</h3>
            <p>
                <label for="header_banner_order"><strong>Thứ tự banner:</strong></label><br>
                <input type="text" id="header_banner_order" name="header_banner_order" value="<?php echo esc_attr($header_banner_order); ?>" class="regular-text" />
            </p>
            <p class="description">Nhập thứ tự banner theo số (1–5), phân cách bằng dấu phẩy. Ví dụ: <code>1,2,3,4,5</code> hoặc <code>5,4,3,2,1</code>.</p>

            <p class="submit">
                <input type="submit" name="dnttvn_save_banner_header" class="button button-primary" value="Lưu tất cả Banner Header">
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        $('.upload-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var index = button.data('index');
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Chọn Banner Header',
                button: {
                    text: 'Sử dụng banner này'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                preview.html('<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px;">');
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px;"></video>');
                }
            });

            mediaUploader.open();
        });

        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            input.val('');
            preview.html('');
        });
    });
    </script>
    <?php
}

// Ad Banner Settings Page (VVIP, VIP, Standard)
function dnttvn_ad_banner_settings_page() {
    // Xử lý lưu dữ liệu banner quảng cáo
    $has_save_action = isset($_POST['dnttvn_save_banner_vvip']) ||
                      isset($_POST['dnttvn_save_banner_vip']) ||
                      isset($_POST['dnttvn_save_banner_standard']) ||
                      isset($_POST['dnttvn_save_banners']);

    if ($has_save_action && check_admin_referer('dnttvn_save_ad_banners_action', 'dnttvn_save_ad_banners_nonce')) {
        $is_global      = isset($_POST['dnttvn_save_banners']);
        $save_vvip      = $is_global || isset($_POST['dnttvn_save_banner_vvip']);
        $save_vip       = $is_global || isset($_POST['dnttvn_save_banner_vip']);
        $save_standard  = $is_global || isset($_POST['dnttvn_save_banner_standard']);

        // Save VVIP Ad Blocks
        if ($save_vvip) {
            if (isset($_POST['vvip_banners'])) {
                update_option('dnttvn_vvip_banners', array_map('absint', $_POST['vvip_banners']));
            }
            if (isset($_POST['vvip_links'])) {
                update_option('dnttvn_vvip_links', array_map('esc_url_raw', $_POST['vvip_links']));
            }
            // Lưu thời gian hiển thị VVIP
            if (isset($_POST['vvip_start']) && is_array($_POST['vvip_start'])) {
                update_option('dnttvn_vvip_start', array_map('sanitize_text_field', $_POST['vvip_start']));
            }
            if (isset($_POST['vvip_end']) && is_array($_POST['vvip_end'])) {
                update_option('dnttvn_vvip_end', array_map('sanitize_text_field', $_POST['vvip_end']));
            }
        }

        // Save VIP Ad Blocks
        if ($save_vip) {
            if (isset($_POST['vip_banners'])) {
                update_option('dnttvn_vip_banners', array_map('absint', $_POST['vip_banners']));
            }
            if (isset($_POST['vip_links'])) {
                update_option('dnttvn_vip_links', array_map('esc_url_raw', $_POST['vip_links']));
            }
            // Lưu thời gian hiển thị VIP
            if (isset($_POST['vip_start']) && is_array($_POST['vip_start'])) {
                update_option('dnttvn_vip_start', array_map('sanitize_text_field', $_POST['vip_start']));
            }
            if (isset($_POST['vip_end']) && is_array($_POST['vip_end'])) {
                update_option('dnttvn_vip_end', array_map('sanitize_text_field', $_POST['vip_end']));
            }
        }

        // Save Standard Ad Blocks
        if ($save_standard) {
            if (isset($_POST['standard_banners'])) {
                update_option('dnttvn_standard_banners', array_map('absint', $_POST['standard_banners']));
            }
            if (isset($_POST['standard_links'])) {
                update_option('dnttvn_standard_links', array_map('esc_url_raw', $_POST['standard_links']));
            }
            // Lưu thời gian hiển thị Standard
            if (isset($_POST['standard_start']) && is_array($_POST['standard_start'])) {
                update_option('dnttvn_standard_start', array_map('sanitize_text_field', $_POST['standard_start']));
            }
            if (isset($_POST['standard_end']) && is_array($_POST['standard_end'])) {
                update_option('dnttvn_standard_end', array_map('sanitize_text_field', $_POST['standard_end']));
            }
        }

        // Save Banner Order (Thứ tự hiển thị cột phải)
        if ($is_global && isset($_POST['banner_column_order'])) {
            update_option('dnttvn_banner_column_order', sanitize_text_field($_POST['banner_column_order']));
        }

        echo '<div class="notice notice-success"><p>Đã lưu banner quảng cáo thành công!</p></div>';
    }

    // Load data
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    $vvip_banners  = get_option('dnttvn_vvip_banners', array());
    $vvip_links    = get_option('dnttvn_vvip_links', array());
    $vvip_start    = get_option('dnttvn_vvip_start', array());
    $vvip_end      = get_option('dnttvn_vvip_end', array());
    $vip_banners   = get_option('dnttvn_vip_banners', array());
    $vip_links     = get_option('dnttvn_vip_links', array());
    $vip_start     = get_option('dnttvn_vip_start', array());
    $vip_end       = get_option('dnttvn_vip_end', array());
    $standard_banners = get_option('dnttvn_standard_banners', array());
    $standard_links   = get_option('dnttvn_standard_links', array());
    $standard_start   = get_option('dnttvn_standard_start', array());
    $standard_end     = get_option('dnttvn_standard_end', array());

    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner Quảng cáo</h1>
        <p class="description">Quản lý banner và video quảng cáo hiển thị ở cột bên phải trang "Danh sách Doanh nghiệp". Hỗ trợ cả hình ảnh và video.</p>

        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_ad_banners_action', 'dnttvn_save_ad_banners_nonce'); ?>

            <h2>Banner VVIP</h2>
            <p class="description">Có thể thêm nhiều banner VVIP, hệ thống sẽ hiển thị theo thứ tự bạn cấu hình.</p>
            <div id="vvip-banners-container">
                <?php
                $vvip_count = max(2, count($vvip_banners));
                for ($i = 0; $i < $vvip_count; $i++) {
                    $banner_id   = isset($vvip_banners[$i]) ? $vvip_banners[$i] : '';
                    $banner_url  = isset($vvip_links[$i]) ? $vvip_links[$i] : '';
                    $start_value = isset($vvip_start[$i]) ? $vvip_start[$i] : '';
                    $end_value   = isset($vvip_end[$i]) ? $vvip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VVIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Banner VVIP <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="vvip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) :
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vvip_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vvip_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vvip_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vvip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VVIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vvip-banner" class="button">+ Thêm Banner VVIP</button>
            </p>

            <h2>Banner VIP</h2>
            <p class="description">Có thể thêm nhiều banner VIP.</p>
            <div id="vip-banners-container">
                <?php
                $vip_count = max(2, count($vip_banners));
                for ($i = 0; $i < $vip_count; $i++) {
                    $banner_id   = isset($vip_banners[$i]) ? $vip_banners[$i] : '';
                    $banner_url  = isset($vip_links[$i]) ? $vip_links[$i] : '';
                    $start_value = isset($vip_start[$i]) ? $vip_start[$i] : '';
                    $end_value   = isset($vip_end[$i]) ? $vip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Banner VIP <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="vip_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) :
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vip_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vip_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vip_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vip-banner" class="button">+ Thêm Banner VIP</button>
            </p>

            <h2>Banner Standard</h2>
            <p class="description">Có thể thêm nhiều banner Standard.</p>
            <div id="standard-banners-container">
                <?php
                $standard_count = max(2, count($standard_banners));
                for ($i = 0; $i < $standard_count; $i++) {
                    $banner_id   = isset($standard_banners[$i]) ? $standard_banners[$i] : '';
                    $banner_url  = isset($standard_links[$i]) ? $standard_links[$i] : '';
                    $start_value = isset($standard_start[$i]) ? $standard_start[$i] : '';
                    $end_value   = isset($standard_end[$i]) ? $standard_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Standard <?php echo $i + 1; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Banner Standard <?php echo $i + 1; ?>:</strong></label><br>
                            <input type="hidden" name="standard_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/video</button>
                            <button type="button" class="button remove-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) :
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="standard_links[<?php echo $i; ?>]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="standard_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="standard_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_standard" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Standard <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-standard-banner" class="button">+ Thêm Banner Standard</button>
            </p>

            <h2>Thứ tự hiển thị Banner</h2>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                <p>
                    <label for="banner_column_order"><strong>Thứ tự hiển thị các loại banner:</strong></label><br>
                    <select name="banner_column_order" id="banner_column_order" style="min-width: 300px; padding: 8px;">
                        <option value="vvip,vip,standard" <?php selected($banner_column_order, 'vvip,vip,standard'); ?>>VVIP → VIP → Standard</option>
                        <option value="vvip,standard,vip" <?php selected($banner_column_order, 'vvip,standard,vip'); ?>>VVIP → Standard → VIP</option>
                        <option value="vip,vvip,standard" <?php selected($banner_column_order, 'vip,vvip,standard'); ?>>VIP → VVIP → Standard</option>
                        <option value="vip,standard,vvip" <?php selected($banner_column_order, 'vip,standard,vvip'); ?>>VIP → Standard → VVIP</option>
                        <option value="standard,vvip,vip" <?php selected($banner_column_order, 'standard,vvip,vip'); ?>>Standard → VVIP → VIP</option>
                        <option value="standard,vip,vvip" <?php selected($banner_column_order, 'standard,vip,vvip'); ?>>Standard → VIP → VVIP</option>
                    </select>
                </p>
                <p class="description">Chọn thứ tự hiển thị các loại banner ở cột bên phải trang doanh nghiệp.</p>
            </div>

            <p class="submit">
                <input type="submit" name="dnttvn_save_banners" class="button button-primary" value="Lưu Banner Quảng cáo">
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        $('.upload-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var index = button.data('index');
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Chọn Banner Quảng cáo',
                button: {
                    text: 'Sử dụng banner này'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                if (attachment.type === 'video') {
                    preview.html('<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px;"></video><p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: ' + attachment.id + '</p>');
                } else {
                    preview.html('<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px;">');
                }
            });

            mediaUploader.open();
        });

        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');

            input.val('');
            preview.html('');
        });

        // Add VVIP Banner
        $('#add-vvip-banner').on('click', function() {
            var container = $('#vvip-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner VVIP ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner VVIP ${count + 1}:</strong></label><br>
                        <input type="hidden" name="vvip_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="vvip_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="vvip_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="vvip_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_vvip" value="${count}" class="button button-secondary">Lưu Banner VVIP ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
        });

        // Add VIP Banner
        $('#add-vip-banner').on('click', function() {
            var container = $('#vip-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner VIP ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner VIP ${count + 1}:</strong></label><br>
                        <input type="hidden" name="vip_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="vip" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="vip" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="vip_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="vip_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="vip_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_vip" value="${count}" class="button button-secondary">Lưu Banner VIP ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
        });

        // Add Standard Banner
        $('#add-standard-banner').on('click', function() {
            var container = $('#standard-banners-container');
            var count = container.children('.banner-item').length;
            var newBanner = `
                <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                    <h3>Banner Standard ${count + 1}</h3>
                    <p>
                        <label><strong>Hình ảnh/Video Banner Standard ${count + 1}:</strong></label><br>
                        <input type="hidden" name="standard_banners[${count}]" class="banner-image-id" value="">
                        <button type="button" class="button button-primary upload-banner-btn" data-type="standard" data-index="${count}">📁 Chọn hình ảnh/video</button>
                        <button type="button" class="button remove-banner-btn" data-type="standard" data-index="${count}">🗑️ Xóa</button>
                        <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước dọc, định dạng JPG/PNG/MP4.</span>
                    </p>
                    <div class="banner-preview" style="margin-top: 10px;"></div>
                    <p>
                        <label>Link (nếu có):</label><br>
                        <input type="url" name="standard_links[${count}]" class="regular-text" placeholder="https://...">
                    </p>
                    <p>
                        <label><strong>Thời gian hiển thị:</strong></label><br>
                        <input type="datetime-local" name="standard_start[${count}]" style="max-width: 220px;">
                        đến
                        <input type="datetime-local" name="standard_end[${count}]" style="max-width: 220px;">
                        <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                    </p>
                    <p>
                        <button type="submit" name="dnttvn_save_banner_standard" value="${count}" class="button button-secondary">Lưu Banner Standard ${count + 1}</button>
                    </p>
                </div>
            `;
            container.append(newBanner);
        });
    });
    </script>
    <?php
}

// Create Admin Settings Pages
function dnttvn_add_admin_menu() {
    // Menu chính: Quản lý Banner Header
    add_menu_page(
        'Quản lý Banner Header',
        'Banner Header',
        'manage_options',
        'dnttvn-banner-header',
        'dnttvn_header_banner_settings_page',
        'dashicons-images-alt2',
        30
    );

    // Submenu: Quản lý Banner Quảng cáo (VVIP, VIP, Standard)
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Banner Quảng cáo',
        'Banner Quảng cáo',
        'manage_options',
        'dnttvn-banner-ads',
        'dnttvn_ad_banner_settings_page'
    );

    // Submenu: Quản lý Link Cộng đồng
    add_submenu_page(
        'dnttvn-banner-header',
        'Quản lý Link Cộng đồng',
        'Link Cộng đồng',
        'manage_options',
        'dnttvn-community-links',
        'dnttvn_community_links_page'
    );
}
add_action('admin_menu', 'dnttvn_add_admin_menu');

// AJAX handlers for community links
add_action('wp_ajax_save_individual_community_link', 'dnttvn_save_individual_community_link');
add_action('wp_ajax_delete_community_link', 'dnttvn_delete_community_link');

// Banner Settings Page - REMOVED (Split into separate pages)
function dnttvn_banner_settings_page_removed() {
    // Xử lý lưu dữ liệu khi bấm các nút Lưu (tổng hoặc từng ô)
    $has_save_action =
        isset($_POST['dnttvn_save_banners']) ||
        isset($_POST['dnttvn_save_banner_header']) ||
        isset($_POST['dnttvn_save_banner_vvip']) ||
        isset($_POST['dnttvn_save_banner_vip']) ||
        isset($_POST['dnttvn_save_banner_standard']);

    if ($has_save_action && check_admin_referer('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce')) {
        $is_global      = isset($_POST['dnttvn_save_banners']);
        $save_header    = $is_global || isset($_POST['dnttvn_save_banner_header']);
        $save_vvip      = $is_global || isset($_POST['dnttvn_save_banner_vvip']);
        $save_vip       = $is_global || isset($_POST['dnttvn_save_banner_vip']);
        $save_standard  = $is_global || isset($_POST['dnttvn_save_banner_standard']);

        // Save Header Banners (for header slider)
        if ($save_header) {
            if (isset($_POST['header_banners']) && is_array($_POST['header_banners'])) {
                // Lưu vào theme_mod cũ để tương thích (1–5)
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id = isset($_POST['header_banners'][$i]) ? absint($_POST['header_banners'][$i]) : 0;
                    set_theme_mod('dnttvn_banner_' . $i, $banner_id);
                }
            }
            // Lưu thời gian hiển thị cho từng Banner Header
            $header_start = isset($_POST['header_start']) && is_array($_POST['header_start'])
                ? array_map('sanitize_text_field', $_POST['header_start'])
                : array();
            $header_end   = isset($_POST['header_end']) && is_array($_POST['header_end'])
                ? array_map('sanitize_text_field', $_POST['header_end'])
                : array();
            update_option('dnttvn_header_start', $header_start);
            update_option('dnttvn_header_end', $header_end);

            if (isset($_POST['header_banner_order'])) {
                set_theme_mod('dnttvn_banner_order', sanitize_text_field($_POST['header_banner_order']));
            }
        }
        
        // Save VVIP Ad Blocks
        if ($save_vvip) {
            if (isset($_POST['vvip_banners'])) {
                update_option('dnttvn_vvip_banners', array_map('absint', $_POST['vvip_banners']));
            }
            if (isset($_POST['vvip_links'])) {
                update_option('dnttvn_vvip_links', array_map('esc_url_raw', $_POST['vvip_links']));
            }
            // Lưu thời gian hiển thị VVIP
            if (isset($_POST['vvip_start']) && is_array($_POST['vvip_start'])) {
                update_option('dnttvn_vvip_start', array_map('sanitize_text_field', $_POST['vvip_start']));
            }
            if (isset($_POST['vvip_end']) && is_array($_POST['vvip_end'])) {
                update_option('dnttvn_vvip_end', array_map('sanitize_text_field', $_POST['vvip_end']));
            }
        }
        
        // Save VIP Ad Blocks
        if ($save_vip) {
            if (isset($_POST['vip_banners'])) {
                update_option('dnttvn_vip_banners', array_map('absint', $_POST['vip_banners']));
            }
            if (isset($_POST['vip_links'])) {
                update_option('dnttvn_vip_links', array_map('esc_url_raw', $_POST['vip_links']));
            }
            // Lưu thời gian hiển thị VIP
            if (isset($_POST['vip_start']) && is_array($_POST['vip_start'])) {
                update_option('dnttvn_vip_start', array_map('sanitize_text_field', $_POST['vip_start']));
            }
            if (isset($_POST['vip_end']) && is_array($_POST['vip_end'])) {
                update_option('dnttvn_vip_end', array_map('sanitize_text_field', $_POST['vip_end']));
            }
        }
        
        // Save Standard Ad Blocks
        if ($save_standard) {
            if (isset($_POST['standard_banners'])) {
                update_option('dnttvn_standard_banners', array_map('absint', $_POST['standard_banners']));
            }
            if (isset($_POST['standard_links'])) {
                update_option('dnttvn_standard_links', array_map('esc_url_raw', $_POST['standard_links']));
            }
            // Lưu thời gian hiển thị Standard
            if (isset($_POST['standard_start']) && is_array($_POST['standard_start'])) {
                update_option('dnttvn_standard_start', array_map('sanitize_text_field', $_POST['standard_start']));
            }
            if (isset($_POST['standard_end']) && is_array($_POST['standard_end'])) {
                update_option('dnttvn_standard_end', array_map('sanitize_text_field', $_POST['standard_end']));
            }
        }
        
        // Save Banner Order (Thứ tự hiển thị cột phải)
        if ($is_global && isset($_POST['banner_column_order'])) {
            update_option('dnttvn_banner_column_order', sanitize_text_field($_POST['banner_column_order']));
        }
        
        echo '<div class="notice notice-success"><p>Đã lưu banner thành công!</p></div>';
    }
    
    // Header banners (for header slider)
    $header_banners = array();
    for ($i = 1; $i <= 5; $i++) {
        $header_banners[$i] = get_theme_mod('dnttvn_banner_' . $i, '');
    }
    $header_banner_order = get_theme_mod('dnttvn_banner_order', '1,2,3,4,5');
    $header_start        = get_option('dnttvn_header_start', array());
    $header_end          = get_option('dnttvn_header_end', array());
    
    $banner_column_order = get_option('dnttvn_banner_column_order', 'vvip,vip,standard');
    
    $vvip_banners  = get_option('dnttvn_vvip_banners', array());
    $vvip_links    = get_option('dnttvn_vvip_links', array());
    $vvip_start    = get_option('dnttvn_vvip_start', array());
    $vvip_end      = get_option('dnttvn_vvip_end', array());
    $vip_banners   = get_option('dnttvn_vip_banners', array());
    $vip_links     = get_option('dnttvn_vip_links', array());
    $vip_start     = get_option('dnttvn_vip_start', array());
    $vip_end       = get_option('dnttvn_vip_end', array());
    $standard_banners = get_option('dnttvn_standard_banners', array());
    $standard_links   = get_option('dnttvn_standard_links', array());
    $standard_start   = get_option('dnttvn_standard_start', array());
    $standard_end     = get_option('dnttvn_standard_end', array());
    
    wp_enqueue_media();
    ?>
    <div class="wrap">
        <h1>Quản lý Banner & Video Quảng cáo</h1>
        <p class="description">Quản lý banner và video quảng cáo hiển thị ở cột bên phải trang "Danh sách Doanh nghiệp". Hỗ trợ cả hình ảnh và video.</p>
        <form method="post" action="">
            <?php wp_nonce_field('dnttvn_save_banners_action', 'dnttvn_save_banners_nonce'); ?>
            
            <h2>Banner Header (Slider trên đầu trang)</h2>
            <p class="description">Chọn tối đa 5 banner để hiển thị trong slider ở phần đầu trang (Header). Thứ tự hiển thị có thể thay đổi bên dưới.</p>
            <div id="header-banners-container">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $banner_id    = isset($header_banners[$i]) ? $header_banners[$i] : '';
                    $start_value  = isset($header_start[$i]) ? $header_start[$i] : '';
                    $end_value    = isset($header_end[$i]) ? $header_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Header <?php echo $i; ?></h3>
                        <p>
                            <label><strong>Hình ảnh Banner Header <?php echo $i; ?>:</strong></label><br>
                            <input type="hidden" name="header_banners[<?php echo $i; ?>]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="header" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh</button>
                            <button type="button" class="button remove-banner-btn" data-type="header" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Khuyến nghị: kích thước ngang, định dạng JPG/PNG.</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php
                            if ($banner_id) {
                                echo wp_get_attachment_image($banner_id, 'medium');
                            }
                            ?>
                        </div>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="header_start[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="header_end[<?php echo $i; ?>]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_header" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Header <?php echo $i; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <h3>Thứ tự hiển thị Banner Header</h3>
            <p>
                <label for="header_banner_order"><strong>Thứ tự banner:</strong></label><br>
                <input type="text" id="header_banner_order" name="header_banner_order" value="<?php echo esc_attr($header_banner_order); ?>" class="regular-text" />
            </p>
            <p class="description">Nhập thứ tự banner theo số (1–5), phân cách bằng dấu phẩy. Ví dụ: <code>1,2,3,4,5</code> hoặc <code>5,4,3,2,1</code>.</p>
            
            <h2>Banner VVIP</h2>
            <p class="description">Có thể thêm nhiều banner VVIP, hệ thống sẽ hiển thị theo thứ tự bạn cấu hình.</p>
            <div id="vvip-banners-container">
                <?php
                $vvip_count = max(2, count($vvip_banners));
                for ($i = 0; $i < $vvip_count; $i++) {
                    $banner_id   = isset($vvip_banners[$i]) ? $vvip_banners[$i] : '';
                    $banner_url  = isset($vvip_links[$i]) ? $vvip_links[$i] : '';
                    $start_value = isset($vvip_start[$i]) ? $vvip_start[$i] : '';
                    $end_value   = isset($vvip_end[$i]) ? $vvip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VVIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label><strong>Hình ảnh/Video Quảng cáo:</strong></label><br>
                            <input type="hidden" name="vvip_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button button-primary upload-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">📁 Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vvip" data-index="<?php echo $i; ?>">🗑️ Xóa</button>
                            <span class="description" style="margin-left: 10px;">Hỗ trợ: JPG, PNG, GIF, MP4, WebM</span>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vvip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vvip_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vvip_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vvip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VVIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vvip-banner" class="button">+ Thêm Banner VVIP</button>
            </p>
            
            <h2>Banner VIP</h2>
            <p class="description">Có thể thêm nhiều banner VIP.</p>
            <div id="vip-banners-container">
                <?php
                $vip_count = max(2, count($vip_banners));
                for ($i = 0; $i < $vip_count; $i++) {
                    $banner_id   = isset($vip_banners[$i]) ? $vip_banners[$i] : '';
                    $banner_url  = isset($vip_links[$i]) ? $vip_links[$i] : '';
                    $start_value = isset($vip_start[$i]) ? $vip_start[$i] : '';
                    $end_value   = isset($vip_end[$i]) ? $vip_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner VIP <?php echo $i + 1; ?></h3>
                        <p>
                            <label>Hình ảnh/Video:</label><br>
                            <input type="hidden" name="vip_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button upload-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="vip" data-index="<?php echo $i; ?>">Xóa</button>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="vip_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="vip_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="vip_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_vip" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner VIP <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-vip-banner" class="button">+ Thêm Banner VIP</button>
            </p>
            
            <h2>Banner Standard</h2>
            <p class="description">Có thể thêm nhiều banner Standard.</p>
            <div id="standard-banners-container">
                <?php
                $standard_count = max(2, count($standard_banners));
                for ($i = 0; $i < $standard_count; $i++) {
                    $banner_id   = isset($standard_banners[$i]) ? $standard_banners[$i] : '';
                    $banner_url  = isset($standard_links[$i]) ? $standard_links[$i] : '';
                    $start_value = isset($standard_start[$i]) ? $standard_start[$i] : '';
                    $end_value   = isset($standard_end[$i]) ? $standard_end[$i] : '';
                    ?>
                    <div class="banner-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
                        <h3>Banner Standard <?php echo $i + 1; ?></h3>
                        <p>
                            <label>Hình ảnh/Video:</label><br>
                            <input type="hidden" name="standard_banners[]" class="banner-image-id" value="<?php echo esc_attr($banner_id); ?>">
                            <button type="button" class="button upload-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Chọn hình ảnh/Video</button>
                            <button type="button" class="button remove-banner-btn" data-type="standard" data-index="<?php echo $i; ?>">Xóa</button>
                        </p>
                        <div class="banner-preview" style="margin-top: 10px;">
                            <?php if ($banner_id) : 
                                $mime_type = get_post_mime_type($banner_id);
                                if (strpos($mime_type, 'video') !== false) :
                                    $video_url = wp_get_attachment_url($banner_id);
                                    ?>
                                    <video src="<?php echo esc_url($video_url); ?>" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>
                                    <p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: <?php echo esc_html($banner_id); ?></p>
                                <?php else :
                                    echo wp_get_attachment_image($banner_id, 'medium');
                                endif;
                            endif; ?>
                        </div>
                        <p>
                            <label>Link (nếu có):</label><br>
                            <input type="url" name="standard_links[]" value="<?php echo esc_attr($banner_url); ?>" class="regular-text" placeholder="https://...">
                        </p>
                        <p>
                            <label><strong>Thời gian hiển thị:</strong></label><br>
                            <input type="datetime-local"
                                   name="standard_start[]"
                                   value="<?php echo esc_attr($start_value); ?>"
                                   style="max-width: 220px;">
                            đến
                            <input type="datetime-local"
                                   name="standard_end[]"
                                   value="<?php echo esc_attr($end_value); ?>"
                                   style="max-width: 220px;">
                            <br><span class="description">Để trống nếu muốn hiển thị không giới hạn thời gian.</span>
                        </p>
                        <p>
                            <button type="submit" name="dnttvn_save_banner_standard" value="<?php echo $i; ?>" class="button button-secondary">Lưu Banner Standard <?php echo $i + 1; ?></button>
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <p>
                <button type="button" id="add-standard-banner" class="button">+ Thêm Banner Standard</button>
            </p>
            
            <h2 style="margin-top: 40px;">Thứ tự hiển thị Banner</h2>
            <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 20px;">
                <p>
                    <label for="banner_column_order"><strong>Thứ tự hiển thị các loại banner:</strong></label><br>
                    <select name="banner_column_order" id="banner_column_order" style="min-width: 300px; padding: 8px;">
                        <option value="vvip,vip,standard" <?php selected($banner_column_order, 'vvip,vip,standard'); ?>>VVIP → VIP → Standard</option>
                        <option value="vvip,standard,vip" <?php selected($banner_column_order, 'vvip,standard,vip'); ?>>VVIP → Standard → VIP</option>
                        <option value="vip,vvip,standard" <?php selected($banner_column_order, 'vip,vvip,standard'); ?>>VIP → VVIP → Standard</option>
                        <option value="vip,standard,vvip" <?php selected($banner_column_order, 'vip,standard,vvip'); ?>>VIP → Standard → VVIP</option>
                        <option value="standard,vvip,vip" <?php selected($banner_column_order, 'standard,vvip,vip'); ?>>Standard → VVIP → VIP</option>
                        <option value="standard,vip,vvip" <?php selected($banner_column_order, 'standard,vip,vvip'); ?>>Standard → VIP → VVIP</option>
                    </select>
                </p>
                <p class="description">Chọn thứ tự hiển thị các loại banner ở cột bên phải trang doanh nghiệp.</p>
            </div>
            
            <p class="submit">
                <input type="submit" name="dnttvn_save_banners" class="button button-primary" value="Lưu Banner">
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('.upload-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var type = button.data('type');
            var index = button.data('index');
            var container = button.closest('.banner-item');
            var input = container.find('.banner-image-id');
            var preview = container.find('.banner-preview');
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Chọn Banner',
                button: {
                    text: 'Sử dụng hình ảnh này'
                },
                multiple: false,
                library: {
                    type: ['image', 'video']
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                input.val(attachment.id);
                var previewHtml = '';
                if (attachment.type === 'image') {
                    previewHtml = '<img src="' + attachment.url + '" style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;">';
                    previewHtml += '<p style="margin-top: 5px; color: #666; font-size: 12px;">Hình ảnh ID: ' + attachment.id + '</p>';
                } else if (attachment.type === 'video') {
                    previewHtml = '<video src="' + attachment.url + '" controls style="max-width: 300px; max-height: 200px; display: block; margin-top: 10px;"></video>';
                    previewHtml += '<p style="margin-top: 5px; color: #666; font-size: 12px;">Video ID: ' + attachment.id + ' (' + attachment.mime + ')</p>';
                }
                preview.html(previewHtml);
            });
            
            mediaUploader.open();
        });
        
        $('.remove-banner-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var container = button.closest('.banner-item');
            container.find('.banner-image-id').val('');
            container.find('.banner-preview').html('');
            container.find('input[type="url"]').val('');
        });
    });
    </script>
    <?php
}

// Get community links for sidebar display
function dnttvn_get_community_links() {
    $links = get_option('dnttvn_community_links', array());

    // Fallback to default if empty
    if (empty($links)) {
        $links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    return $links;
}

// Community Links Settings Page - Enhanced
function dnttvn_community_links_page() {
    // Xử lý lưu dữ liệu khi submit form (fallback)
    if (isset($_POST['dnttvn_save_all_community_links']) && check_admin_referer('dnttvn_community_links_nonce')) {
        $links = array();

        if (isset($_POST['community_link_names']) && isset($_POST['community_link_urls'])) {
            $names = $_POST['community_link_names'];
            $urls = $_POST['community_link_urls'];

            foreach ($names as $index => $name) {
                $name = sanitize_text_field($name);
                $url = isset($urls[$index]) ? esc_url_raw($urls[$index]) : '';

                if (!empty($name)) {
                    $links[] = array(
                        'name' => $name,
                        'url' => $url
                    );
                }
            }
        }

        update_option('dnttvn_community_links', $links);
        echo '<div class="notice notice-success"><p>Đã lưu tất cả các liên kết cộng đồng!</p></div>';
    }

    // Lấy dữ liệu hiện tại
    $community_links = get_option('dnttvn_community_links', array());

    // Mặc định nếu chưa có dữ liệu
    if (empty($community_links)) {
        $community_links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    ?>
    <div class="wrap">
        <h1>Quản lý Link Cộng đồng</h1>
        <p class="description">Quản lý các liên kết hiển thị ở cột phải của website. Bạn có thể thêm không giới hạn liên kết, lưu từng liên kết riêng biệt hoặc lưu tất cả cùng lúc.</p>

        <div id="messages-container"></div>

        <div id="community-links-container">
            <?php foreach ($community_links as $index => $link) : ?>
            <div class="community-link-item" data-index="<?php echo $index; ?>" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h3 style="margin: 0; color: #23282d;">Liên kết <?php echo $index + 1; ?></h3>
                    <button type="button" class="button button-small remove-community-link" style="background: #dc3232; color: white; border-color: #dc3232;" title="Xóa liên kết này">🗑️ Xóa</button>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>Tên hiển thị:</strong></label>
                        <input type="text"
                               class="link-name regular-text"
                               value="<?php echo esc_attr($link['name']); ?>"
                               placeholder="Ví dụ: Cộng đồng ABC"
                               style="width: 100%;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>URL:</strong></label>
                        <input type="url"
                               class="link-url regular-text"
                               value="<?php echo esc_attr($link['url']); ?>"
                               placeholder="https://..."
                               style="width: 100%;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="button" class="button button-primary save-individual-link" data-index="<?php echo $index; ?>">
                        💾 Lưu liên kết này
                    </button>
                    <span class="save-status" style="line-height: 30px; color: #666;"></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="margin: 30px 0; padding: 20px; background: #fff; border: 2px dashed #007cba; border-radius: 6px; text-align: center;">
            <button type="button" id="add-community-link" class="button button-primary button-hero" style="font-size: 16px; padding: 12px 24px;">
                ➕ Thêm liên kết cộng đồng mới
            </button>
            <p style="margin: 10px 0 0 0; color: #666;">Không giới hạn số lượng liên kết</p>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #f1f1f1; border-radius: 6px;">
            <h3 style="margin-top: 0; color: #23282d;">💡 Mẹo sử dụng:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Lưu từng liên kết:</strong> Nhấn "Lưu liên kết này" để lưu ngay lập tức mà không ảnh hưởng đến các liên kết khác</li>
                <li><strong>Thêm liên kết:</strong> Nhấn nút "Thêm liên kết cộng đồng mới" để tạo liên kết mới</li>
                <li><strong>Xóa liên kết:</strong> Nhấn nút "Xóa" ở góc phải để xóa liên kết</li>
                <li><strong>Lưu tất cả:</strong> Sử dụng nút "Lưu tất cả" ở cuối trang nếu muốn cập nhật đồng thời</li>
            </ul>
        </div>

        <form method="post" style="margin-top: 30px;">
            <?php wp_nonce_field('dnttvn_community_links_nonce'); ?>

            <!-- Hidden fields for fallback form submission -->
            <div id="hidden-form-fields"></div>

            <p class="submit">
                <input type="submit" name="dnttvn_save_all_community_links" class="button button-primary button-large" value="💾 Lưu tất cả liên kết" style="font-size: 16px; padding: 12px 24px;">
            </p>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        var linkIndex = <?php echo count($community_links); ?>;
        var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var nonce = '<?php echo wp_create_nonce('dnttvn_community_links_nonce'); ?>';

        // Hàm hiển thị thông báo
        function showMessage(message, type = 'success') {
            var $container = $('#messages-container');
            var bgColor = type === 'success' ? '#d4edda' : '#f8d7da';
            var textColor = type === 'success' ? '#155724' : '#721c24';
            var borderColor = type === 'success' ? '#c3e6cb' : '#f5c6cb';

            var $notice = $('<div class="notice notice-' + type + ' is-dismissible" style="margin: 0 0 20px 0; padding: 12px 15px; background: ' + bgColor + '; border: 1px solid ' + borderColor + '; color: ' + textColor + '; border-radius: 4px;">' +
                '<p>' + message + '</p>' +
                '<button type="button" class="notice-dismiss" style="color: ' + textColor + ';"><span class="screen-reader-text">Bỏ qua thông báo này.</span></button>' +
                '</div>');

            $container.html($notice);

            // Auto hide after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() { $(this).remove(); });
            }, 5000);

            // Dismissible
            $notice.find('.notice-dismiss').on('click', function() {
                $notice.fadeOut(function() { $(this).remove(); });
            });
        }

        // Lưu từng liên kết riêng biệt (AJAX)
        $(document).on('click', '.save-individual-link', function() {
            var $button = $(this);
            var $item = $button.closest('.community-link-item');
            var $status = $item.find('.save-status');
            var index = $button.data('index');
            var name = $item.find('.link-name').val().trim();
            var url = $item.find('.link-url').val().trim();

            if (!name) {
                showMessage('Vui lòng nhập tên liên kết!', 'error');
                return;
            }

            $button.prop('disabled', true).text('⏳ Đang lưu...');
            $status.html('<span style="color: #666;">Đang lưu...</span>');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'save_individual_community_link',
                    link_index: index,
                    link_name: name,
                    link_url: url,
                    _wpnonce: nonce
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        showMessage('✅ ' + data.message);
                        $status.html('<span style="color: #28a745;">✓ Đã lưu</span>');
                        $button.text('💾 Lưu liên kết này');
                    } else {
                        showMessage('❌ Lỗi khi lưu liên kết!', 'error');
                        $status.html('<span style="color: #dc3545;">✗ Lỗi</span>');
                        $button.text('💾 Lưu liên kết này');
                    }
                    $button.prop('disabled', false);
                },
                error: function() {
                    showMessage('❌ Lỗi kết nối! Vui lòng thử lại.', 'error');
                    $status.html('<span style="color: #dc3545;">✗ Lỗi</span>');
                    $button.prop('disabled', false).text('💾 Lưu liên kết này');
                }
            });
        });

        // Thêm liên kết mới
        $('#add-community-link').on('click', function() {
            linkIndex++;
            var newLink = `
                <div class="community-link-item" data-index="${linkIndex}" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 6px; position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0; color: #23282d;">Liên kết ${linkIndex + 1}</h3>
                        <button type="button" class="button button-small remove-community-link" style="background: #dc3232; color: white; border-color: #dc3232;" title="Xóa liên kết này">🗑️ Xóa</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>Tên hiển thị:</strong></label>
                            <input type="text" class="link-name regular-text" placeholder="Ví dụ: Cộng đồng ABC" style="width: 100%;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;"><strong>URL:</strong></label>
                            <input type="url" class="link-url regular-text" placeholder="https://..." style="width: 100%;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="button button-primary save-individual-link" data-index="${linkIndex}">
                            💾 Lưu liên kết này
                        </button>
                        <span class="save-status" style="line-height: 30px; color: #666;"></span>
                    </div>
                </div>
            `;
            $('#community-links-container').append(newLink);
            showMessage('✨ Đã thêm liên kết mới! Hãy điền thông tin và nhấn "Lưu liên kết này".');
        });

        // Xóa liên kết (AJAX)
        $(document).on('click', '.remove-community-link', function() {
            var $item = $(this).closest('.community-link-item');
            var index = $item.data('index');

            if (!confirm('Bạn có chắc chắn muốn xóa liên kết này?')) {
                return;
            }

            $item.css('opacity', '0.5');

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'delete_community_link',
                    link_index: index,
                    _wpnonce: nonce
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $item.fadeOut(function() { $(this).remove(); });
                        showMessage('🗑️ ' + data.message);
                        // Re-number remaining items
                        $('.community-link-item').each(function(i) {
                            $(this).find('h3').text('Liên kết ' + (i + 1));
                        });
                    } else {
                        $item.css('opacity', '1');
                        showMessage('❌ Lỗi khi xóa liên kết!', 'error');
                    }
                },
                error: function() {
                    $item.css('opacity', '1');
                    showMessage('❌ Lỗi kết nối! Vui lòng thử lại.', 'error');
                }
            });
        });

        // Cập nhật hidden form fields trước khi submit
        $('form').on('submit', function() {
            $('#hidden-form-fields').empty();
            $('.community-link-item').each(function(index) {
                var name = $(this).find('.link-name').val();
                var url = $(this).find('.link-url').val();
                if (name.trim()) {
                    $('#hidden-form-fields').append(
                        '<input type="hidden" name="community_link_names[]" value="' + name.replace(/"/g, '&quot;') + '">' +
                        '<input type="hidden" name="community_link_urls[]" value="' + url + '">'
                    );
                }
            });
        });
    });
    </script>
    <?php
}

// AJAX Handler: Save individual community link
function dnttvn_save_individual_community_link() {
    check_ajax_referer('dnttvn_community_links_nonce');

    $index = intval($_POST['link_index']);
    $name = sanitize_text_field($_POST['link_name']);
    $url = esc_url_raw($_POST['link_url']);

    if (empty($name)) {
        wp_die(json_encode(array('success' => false, 'message' => 'Tên liên kết không được để trống!')));
    }

    $links = get_option('dnttvn_community_links', array());

    // Nếu chưa có dữ liệu, tạo mặc định
    if (empty($links)) {
        $links = array(
            array('name' => 'Trang Cộng đồng', 'url' => ''),
            array('name' => 'Cộng đồng Doanh nhân Trẻ', 'url' => ''),
            array('name' => 'Cộng đồng Khởi nghiệp', 'url' => ''),
            array('name' => 'Cộng đồng Đầu tư', 'url' => '')
        );
    }

    // Cập nhật hoặc thêm link
    if ($index >= 0 && $index < count($links)) {
        $links[$index] = array('name' => $name, 'url' => $url);
    } else {
        $links[] = array('name' => $name, 'url' => $url);
    }

    $saved = update_option('dnttvn_community_links', $links);

    if ($saved) {
        wp_die(json_encode(array('success' => true, 'message' => 'Đã lưu liên kết thành công!')));
    } else {
        wp_die(json_encode(array('success' => false, 'message' => 'Không có thay đổi để lưu.')));
    }
}

// AJAX Handler: Delete community link
function dnttvn_delete_community_link() {
    check_ajax_referer('dnttvn_community_links_nonce');

    $index = intval($_POST['link_index']);
    $links = get_option('dnttvn_community_links', array());

    if (!isset($links[$index])) {
        wp_die(json_encode(array('success' => false, 'message' => 'Liên kết không tồn tại!')));
    }

    unset($links[$index]);
    $links = array_values($links); // Re-index array

    $saved = update_option('dnttvn_community_links', $links);

    if ($saved) {
        wp_die(json_encode(array('success' => true, 'message' => 'Đã xóa liên kết thành công!')));
    } else {
        wp_die(json_encode(array('success' => false, 'message' => 'Lỗi khi xóa liên kết!')));
    }
}

// Enqueue admin script for banner management
function dnttvn_enqueue_banner_admin_scripts($hook) {
    if ($hook != 'toplevel_page_dnttvn-banner-header' &&
        $hook != 'banner_page_dnttvn-banner-ads' &&
        $hook != 'banner_page_dnttvn-community-links') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_banner_admin_scripts');

// Enqueue jQuery UI Sortable for structured content
function dnttvn_enqueue_structured_content_scripts($hook) {
    global $post_type;
    if (in_array($post_type, array('tin_tuc', 'cong_dong', 'doanh_nghiep')) && ($hook == 'post.php' || $hook == 'post-new.php')) {
        wp_enqueue_script('jquery-ui-sortable');
    }
}
add_action('admin_enqueue_scripts', 'dnttvn_enqueue_structured_content_scripts');
