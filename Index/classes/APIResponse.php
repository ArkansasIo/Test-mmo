<?php
/**
 * API Response Handler - Standardized response formatting
 */
class APIResponse {
    private $status = 'error';
    private $code = 400;
    private $message = '';
    private $data = [];
    
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_VALIDATION_ERROR = 'validation_error';
    const STATUS_UNAUTHORIZED = 'unauthorized';
    const STATUS_FORBIDDEN = 'forbidden';
    const STATUS_NOT_FOUND = 'not_found';
    
    /**
     * Set successful response
     */
    public function success($data = [], $message = 'Operation successful') {
        $this->status = self::STATUS_SUCCESS;
        $this->code = 200;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set error response
     */
    public function error($message = 'An error occurred', $code = 400, $data = []) {
        $this->status = self::STATUS_ERROR;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set validation error response
     */
    public function validationError($errors, $message = 'Validation failed') {
        $this->status = self::STATUS_VALIDATION_ERROR;
        $this->code = 422;
        $this->message = $message;
        $this->data = ['errors' => $errors];
        return $this;
    }
    
    /**
     * Set unauthorized response
     */
    public function unauthorized($message = 'Unauthorized access') {
        $this->status = self::STATUS_UNAUTHORIZED;
        $this->code = 401;
        $this->message = $message;
        $this->data = [];
        return $this;
    }
    
    /**
     * Set forbidden response
     */
    public function forbidden($message = 'Access forbidden') {
        $this->status = self::STATUS_FORBIDDEN;
        $this->code = 403;
        $this->message = $message;
        $this->data = [];
        return $this;
    }
    
    /**
     * Set not found response
     */
    public function notFound($resource = 'Resource', $message = null) {
        $this->status = self::STATUS_NOT_FOUND;
        $this->code = 404;
        $this->message = $message ?? "$resource not found";
        $this->data = [];
        return $this;
    }
    
    /**
     * Send response
     */
    public function send($asJson = true) {
        $response = [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => time()
        ];
        
        if ($asJson) {
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        
        http_response_code($this->code);
        return $response;
    }
    
    /**
     * Get response array
     */
    public function getArray() {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}
