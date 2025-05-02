
<?php
function handleImageUpload($file) {
    $errors = [];
    $imagePath = '';

    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        $fileType = $file['type'];
        
        if (array_key_exists($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = $allowedTypes[$fileType];
            $filename = uniqid() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $imagePath = $destination;
            } else {
                $errors[] = "Failed to upload image.";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed.";
        }
    }

    return [
        'path' => $imagePath,
        'errors' => $errors
    ];
}
