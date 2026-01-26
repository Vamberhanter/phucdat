jQuery(document).ready(function($) {
    // Media Uploader for Hình ảnh phụ
    $('#upload_hinh_anh_phu').on('click', function(e) {
        e.preventDefault();
        
        var mediaUploader = wp.media({
            title: 'Chọn hình ảnh phụ',
            button: {
                text: 'Sử dụng hình ảnh này'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#hinh_anh_phu').val(attachment.id);
        });
        
        mediaUploader.open();
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
});
