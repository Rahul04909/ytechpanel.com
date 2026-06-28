<?php
/**
 * YTech Panels - Admin Add Product Page
 */
include './header.php';

$db = getDB();
$nextOrder = $db->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM products")->fetchColumn();
?>

<style>
    .form-card { background: #fff; border: 1px solid #e2e8f0; margin-bottom: 24px; border-radius: 4px; }
    .form-card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 24px; font-weight: 600; font-size: 15px; color: #1e293b; display: flex; align-items: center; gap: 10px; border-radius: 4px 4px 0 0; }
    .form-card-body { padding: 24px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block; }
    .form-control { border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px 12px; font-size: 14px; width: 100%; transition: border-color 0.2s; }
    .form-control:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 2px rgba(34,113,177,0.1); }
    .upload-area { border: 2px dashed #cbd5e1; border-radius: 6px; padding: 20px; text-align: center; cursor: pointer; background: #f8fafc; transition: all 0.2s; position: relative; overflow: hidden; }
    .upload-area:hover { border-color: #2271b1; background: #f0f4f8; }
    .upload-area input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .upload-area i { font-size: 24px; color: #94a3b8; margin-bottom: 8px; display: block; pointer-events: none; }
    .upload-area p { pointer-events: none; }
    .img-preview { max-width: 100%; max-height: 150px; object-fit: contain; margin-top: 10px; display: none; border-radius: 4px; border: 1px solid #e2e8f0; }
    .gallery-preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .gallery-item { position: relative; width: 100px; height: 100px; border: 1px solid #e2e8f0; border-radius: 4px; overflow: hidden; }
    .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-item .remove-btn { position: absolute; top: 4px; right: 4px; background: rgba(220,38,38,0.9); color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .btn-submit { background: #2271b1; color: #fff; border: none; padding: 10px 24px; font-weight: 600; border-radius: 4px; transition: background 0.2s; }
    .btn-submit:hover { background: #135e96; }
    .section-title { font-size: 16px; font-weight: 600; margin: 24px 0 16px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
</style>

<form id="productForm" enctype="multipart/form-data">
    <input type="hidden" name="action" value="add_product">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-box"></i> General Information</div>
                <div class="form-card-body">
                    <div class="form-group mb-3">
                        <label>Product Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Short Description</label>
                        <textarea class="form-control" name="short_description" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Detailed Description</label>
                        <textarea id="editor" name="description"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-search"></i> SEO & Meta Information</div>
                <div class="form-card-body">
                    <div class="form-group mb-3">
                        <label>Meta Title</label>
                        <input type="text" class="form-control" name="meta_title">
                    </div>
                    <div class="form-group mb-3">
                        <label>Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" placeholder="keyword1, keyword2">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>OG Title</label>
                                <input type="text" class="form-control" name="og_title">
                            </div>
                            <div class="form-group mb-3">
                                <label>OG Description</label>
                                <textarea class="form-control" name="og_description" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>OG Image</label>
                                <input type="file" class="form-control" name="og_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Schema JSON (Advanced)</label>
                        <textarea class="form-control" name="schema_json" rows="4" placeholder='{"@context": "https://schema.org/", "@type": "Product"...}'></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-cogs"></i> Settings</div>
                <div class="form-card-body">
                    <div class="form-group mb-3">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="<?= $nextOrder ?>">
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableCatalog" name="enable_catalog" value="1">
                            <label class="form-check-label" for="enableCatalog" style="display:inline;">Enable PDF Catalog</label>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="catalogUploadDiv" style="display:none;">
                        <label>Upload PDF Catalog</label>
                        <input type="file" class="form-control" name="catalog_pdf" accept="application/pdf">
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-image"></i> Featured Image</div>
                <div class="form-card-body">
                    <div class="upload-area" id="featUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p style="margin:0;font-size:12px;color:#646970;">Click to upload main image</p>
                        <input type="file" name="featured_image" id="featInput" accept="image/*">
                    </div>
                    <img id="featPreview" class="img-preview">
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header"><i class="fas fa-images"></i> Gallery Images</div>
                <div class="form-card-body">
                    <div class="upload-area" id="galleryUploadArea">
                        <i class="fas fa-images"></i>
                        <p style="margin:0;font-size:12px;color:#646970;">Click to select multiple images</p>
                        <input type="file" name="gallery_images[]" id="galleryInput" accept="image/*" multiple>
                    </div>
                    <div id="galleryPreviewContainer" class="gallery-preview-container"></div>
                </div>
            </div>

            <button type="submit" class="btn-submit w-100" id="submitBtn"><i class="fas fa-save"></i> Save Product</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap preview anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking table emoticons template help',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
    });

    $('#enableCatalog').change(function() {
        if($(this).is(':checked')) $('#catalogUploadDiv').slideDown();
        else $('#catalogUploadDiv').slideUp();
    });

    // Featured Image
    $('#featInput').change(function(e) {
        if(e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) { $('#featPreview').attr('src', ev.target.result).show(); };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Gallery Images
    var galleryFiles = [];
    $('#galleryInput').change(function(e) {
        var files = e.target.files;
        for(var i=0; i<files.length; i++) {
            galleryFiles.push(files[i]);
            var reader = new FileReader();
            reader.onload = (function(file, index) {
                return function(ev) {
                    var html = '<div class="gallery-item" data-index="'+index+'">' +
                               '<img src="'+ev.target.result+'">' +
                               '<button type="button" class="remove-btn"><i class="fas fa-times"></i></button>' +
                               '</div>';
                    $('#galleryPreviewContainer').append(html);
                };
            })(files[i], galleryFiles.length - 1);
            reader.readAsDataURL(files[i]);
        }
    });

    $(document).on('click', '.gallery-item .remove-btn', function() {
        var item = $(this).closest('.gallery-item');
        var index = item.data('index');
        galleryFiles[index] = null; // Mark as removed
        item.remove();
    });

    $('#productForm').submit(function(e) {
        e.preventDefault();
        tinymce.triggerSave();
        
        var btn = $('#submitBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        var formData = new FormData(this);
        
        // Re-append valid gallery files (overriding the default input which we don't manipulate directly)
        formData.delete('gallery_images[]');
        for(var i=0; i<galleryFiles.length; i++) {
            if(galleryFiles[i] !== null) {
                formData.append('gallery_images[]', galleryFiles[i]);
            }
        }

        $.ajax({
            url: 'handlers/product-handler.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({icon: 'success', title: 'Saved!', text: res.message, timer: 2000, showConfirmButton: false})
                    .then(function() { window.location.href = 'products.php'; });
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: res.message});
                }
            },
            error: function() { Swal.fire({icon: 'error', title: 'Error', text: 'Network error.'}); },
            complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Product'); }
        });
    });
});
</script>

<?php include './footer.php'; ?>