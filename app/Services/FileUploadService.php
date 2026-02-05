<?php
namespace App\Services;

class FileUploadService
{
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];
    private $maxSize = 10 * 1024 * 1024; // 10MB
    private $uploadPath;
    
    public function __construct()
    {
        // Normalize to always end with a directory separator.
        // UPLOAD_PATH is defined without trailing slash in config/constants.php.
        // Without normalization, concatenations like "$this->uploadPath . $directory"
        // become ".../uploadsproducts" instead of ".../uploads/products".
        $this->uploadPath = rtrim(UPLOAD_PATH, "/\\") . '/';
        
        // Create upload directories if they don't exist
        $this->createDirectories();
    }
    
    private function createDirectories()
    {
        $directories = ['products', 'payments', 'users', 'temp'];
        
        foreach ($directories as $directory) {
            $path = $this->uploadPath . $directory;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
    
    public function upload($file, $folder = 'temp', $filename = null)
    {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception($this->getUploadError($file['error']));
        }
        
        // Validate file size
        if ($file['size'] > $this->maxSize) {
            throw new \Exception('File size exceeds maximum limit of ' . ($this->maxSize / 1024 / 1024) . 'MB');
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate extension
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \Exception('File type not allowed. Allowed types: ' . implode(', ', $this->allowedExtensions));
        }
        
        // Generate filename if not provided
        if (!$filename) {
            $filename = $this->generateFilename($extension);
        }
        
        // Create full path
        $fullPath = $this->uploadPath . $folder . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \Exception('Failed to move uploaded file');
        }
        
        // Set permissions
        chmod($fullPath, 0644);
        
        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'path' => $folder . '/' . $filename,
            'full_path' => $fullPath,
            'size' => $file['size'],
            'extension' => $extension,
            'mime_type' => $file['type']
        ];
    }
    
    private function generateFilename($extension)
    {
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    private function getUploadError($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
    
    public function uploadMultiple($files, $folder = 'temp')
    {
        $results = [];
        
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                ];
                
                try {
                    $results[] = $this->upload($file, $folder);
                } catch (\Exception $e) {
                    $results[] = [
                        'error' => $e->getMessage(),
                        'filename' => $name
                    ];
                }
            }
        }
        
        return $results;
    }
    
    public function delete($filename, $folder = 'temp')
    {
        $path = $this->uploadPath . $folder . '/' . $filename;
        
        if (file_exists($path)) {
            return unlink($path);
        }
        
        return false;
    }
    
    public function resizeImage($sourcePath, $destinationPath, $width = 800, $height = 600, $quality = 85)
    {
        // Check if GD is available
        if (!function_exists('gd_info')) {
            throw new \Exception('GD library not available');
        }
        
        // Get image info
        $info = getimagesize($sourcePath);
        
        if (!$info) {
            throw new \Exception('Invalid image file');
        }
        
        $mime = $info['mime'];
        
        // Create image from source
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type: ' . $mime);
        }
        
        if (!$source) {
            throw new \Exception('Failed to create image from source');
        }
        
        // Get original dimensions
        $originalWidth = imagesx($source);
        $originalHeight = imagesy($source);
        
        // Calculate aspect ratio
        $originalRatio = $originalWidth / $originalHeight;
        $newRatio = $width / $height;
        
        // Calculate new dimensions
        if ($originalRatio > $newRatio) {
            $newWidth = $width;
            $newHeight = $width / $originalRatio;
        } else {
            $newHeight = $height;
            $newWidth = $height * $originalRatio;
        }
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        // Resize image
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Save image
        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($newImage, $destinationPath, $quality);
                break;
            case 'image/png':
                imagepng($newImage, $destinationPath, 9);
                break;
            case 'image/gif':
                imagegif($newImage, $destinationPath);
                break;
            case 'image/webp':
                imagewebp($newImage, $destinationPath, $quality);
                break;
        }
        
        // Free memory
        imagedestroy($source);
        imagedestroy($newImage);
        
        return true;
    }
}