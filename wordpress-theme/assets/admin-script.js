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
    
    // Bulk Edit Save
    $bulkEditor.on('click', '#doaction, #doaction2', function(e) {
        var action = $(this).prev('select').val();
        
        if (action == 'edit') {
            e.preventDefault();
            
            var postIds = [];
            $('tbody th.check-column input[type="checkbox"]:checked').each(function() {
                postIds.push($(this).val());
            });
            
            if (postIds.length > 0) {
                var nganh_hang = $bulkEditor.find('input[name="_nganh_hang"]').val();
                var khu_vuc = $bulkEditor.find('input[name="_khu_vuc"]').val();
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'save_bulk_edit_doanh_nghiep',
                        post_ids: postIds,
                        _nganh_hang: nganh_hang,
                        _khu_vuc: khu_vuc,
                        _wpnonce: $('#_wpnonce').val()
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        }
    });
});
