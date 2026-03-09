<?php
/**
 * Cache Handler - In-memory and filesystem caching
 */
class Cache {
    private $cacheDir;
    private $defaultTTL = 3600; // 1 hour
    private $memory = [];
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../../cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Get value from cache
     */
    public function get($key) {
        // Check memory cache first
        if (isset($this->memory[$key])) {
            return $this->memory[$key]['value'];
        }
        
        // Check file cache
        $filePath = $this->getFilePath($key);
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            $cache = json_decode($data, true);
            
            if ($cache && isset($cache['expires'])) {
                if ($cache['expires'] > time()) {
                    return $cache['value'];
                } else {
                    unlink($filePath);
                }
            }
        }
        
        return null;
    }
    
    /**
     * Set value in cache
     */
    public function set($key, $value, $ttl = null) {
        $ttl = $ttl ?? $this->defaultTTL;
        
        // Store in memory
        $this->memory[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        // Store in file
        $cache = [
            'key' => $key,
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        $filePath = $this->getFilePath($key);
        file_put_contents($filePath, json_encode($cache));
        
        return true;
    }
    
    /**
     * Delete from cache
     */
    public function delete($key) {
        unset($this->memory[$key]);
        
        $filePath = $this->getFilePath($key);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     */
    public function clear() {
        $this->memory = [];
        
        $files = glob($this->cacheDir . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Get cache file path
     */
    private function getFilePath($key) {
        $hash = md5($key);
        return $this->cacheDir . '/' . $hash . '.cache';
    }
    
    /**
     * Get cache stats
     */
    public function getStats() {
        $files = glob($this->cacheDir . '/*.cache');
        $totalSize = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $data = file_get_contents($file);
            $cache = json_decode($data, true);
            
            if ($cache && isset($cache['expires']) && $cache['expires'] < time()) {
                $expired++;
            }
        }
        
        return [
            'cache_files' => count($files),
            'total_size' => $totalSize,
            'memory_items' => count($this->memory),
            'expired_files' => $expired
        ];
    }
}
