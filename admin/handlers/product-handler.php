<?php
/**
 * YTech Panels - Product Handler
 */
require_once dirname(__DIR__, 2) . '/config/db.php';
require_once dirname(__DIR__) . '/auth.php';
requireAdminAuth();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

if (empty($action) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid request or security token.']);
    exit;
}

$db = getDB();
$uploadDir = dirname(__DIR__) . '/uploads/products/';

// Ensure directories exist
$dirs = [
    $uploadDir . 'featured/',
    $uploadDir . 'gallery/',
    $uploadDir . 'catalogs/',
    $uploadDir . 'og/'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function handleUpload($fileArray, $targetDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], $maxSize = 5242880) {
    if (empty($fileArray['name'])) return null;
    
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Upload error code: " . $fileArray['error']);
    }
    
    if (!in_array($fileArray['type'], $allowedTypes)) {
        throw new Exception("Invalid file type: " . $fileArray['type']);
    }
    
    if ($fileArray['size'] > $maxSize) {
        throw new Exception("File too large. Max size is " . ($maxSize/1024/1024) . "MB");
    }
    
    $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    
    if (!move_uploaded_file($fileArray['tmp_name'], $targetDir . $filename)) {
        throw new Exception("Failed to save uploaded file.");
    }
    
    return $filename;
}

try {
    if ($action === 'add_product' || $action === 'edit_product') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $title = trim($_POST['title'] ?? '');
        if (empty($title)) throw new Exception("Title is required.");
        
        $shortDesc = trim($_POST['short_description'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $enableCatalog = isset($_POST['enable_catalog']) ? 1 : 0;
        $metaTitle = trim($_POST['meta_title'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $metaKeywords = trim($_POST['meta_keywords'] ?? '');
        $ogTitle = trim($_POST['og_title'] ?? '');
        $ogDesc = trim($_POST['og_description'] ?? '');
        $schemaJson = trim($_POST['schema_json'] ?? '');
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;

        // Fetch existing product if editing
        $existing = null;
        if ($id > 0) {
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            if (!$existing) throw new Exception("Product not found.");
        }

        // Handle Featured Image
        $featuredImage = $existing['featured_image'] ?? '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newImage = handleUpload($_FILES['featured_image'], $uploadDir . 'featured/');
            if ($newImage) {
                if ($featuredImage && file_exists($uploadDir . 'featured/' . $featuredImage)) unlink($uploadDir . 'featured/' . $featuredImage);
                $featuredImage = $newImage;
            }
        }

        // Handle OG Image
        $ogImage = $existing['og_image'] ?? '';
        if (isset($_FILES['og_image']) && $_FILES['og_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newOg = handleUpload($_FILES['og_image'], $uploadDir . 'og/');
            if ($newOg) {
                if ($ogImage && file_exists($uploadDir . 'og/' . $ogImage)) unlink($uploadDir . 'og/' . $ogImage);
                $ogImage = $newOg;
            }
        }

        // Handle Catalog PDF
        $catalogPdf = $existing['catalog_pdf'] ?? '';
        if ($enableCatalog && isset($_FILES['catalog_pdf']) && $_FILES['catalog_pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newPdf = handleUpload($_FILES['catalog_pdf'], $uploadDir . 'catalogs/', ['application/pdf'], 10485760); // 10MB
            if ($newPdf) {
                if ($catalogPdf && file_exists($uploadDir . 'catalogs/' . $catalogPdf)) unlink($uploadDir . 'catalogs/' . $catalogPdf);
                $catalogPdf = $newPdf;
            }
        }

        // Handle Gallery Images (Append to existing)
        $gallery = $existing ? json_decode($existing['gallery_images'] ?: '[]', true) : [];
        if (!is_array($gallery)) $gallery = [];
        
        // Remove deleted gallery images
        $removeGallery = json_decode($_POST['remove_gallery'] ?? '[]', true);
        if (is_array($removeGallery) && !empty($removeGallery)) {
            foreach ($removeGallery as $fileToRemove) {
                $idx = array_search($fileToRemove, $gallery);
                if ($idx !== false) {
                    unset($gallery[$idx]);
                    if (file_exists($uploadDir . 'gallery/' . $fileToRemove)) unlink($uploadDir . 'gallery/' . $fileToRemove);
                }
            }
            $gallery = array_values($gallery); // Re-index
        }

        // Upload new gallery images
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
            $files = $_FILES['gallery_images'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $singleFile = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];
                    try {
                        $newGal = handleUpload($singleFile, $uploadDir . 'gallery/');
                        if ($newGal) $gallery[] = $newGal;
                    } catch (Exception $e) {
                        // skip individual failed gallery uploads
                    }
                }
            }
        }
        $galleryJson = json_encode($gallery);

        if ($action === 'add_product') {
            $stmt = $db->prepare("
                INSERT INTO products (
                    title, short_description, description, featured_image, gallery_images,
                    enable_catalog, catalog_pdf, meta_title, meta_description, meta_keywords,
                    og_title, og_description, og_image, schema_json, status, sort_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $shortDesc, $description, $featuredImage, $galleryJson,
                $enableCatalog, $catalogPdf, $metaTitle, $metaDesc, $metaKeywords,
                $ogTitle, $ogDesc, $ogImage, $schemaJson, $status, $sortOrder
            ]);
            echo json_encode(['success' => true, 'message' => 'Product added successfully.']);
        } else {
            $stmt = $db->prepare("
                UPDATE products SET
                    title=?, short_description=?, description=?, featured_image=?, gallery_images=?,
                    enable_catalog=?, catalog_pdf=?, meta_title=?, meta_description=?, meta_keywords=?,
                    og_title=?, og_description=?, og_image=?, schema_json=?, status=?, sort_order=?
                WHERE id=?
            ");
            $stmt->execute([
                $title, $shortDesc, $description, $featuredImage, $galleryJson,
                $enableCatalog, $catalogPdf, $metaTitle, $metaDesc, $metaKeywords,
                $ogTitle, $ogDesc, $ogImage, $schemaJson, $status, $sortOrder,
                $id
            ]);
            echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
        }
    } else {
        throw new Exception("Unknown action.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
