<?php

if (!function_exists('convertIdToCode')) {
    /**
     * Convert ID to formatted code
     *
     * @param int $id
     * @return string
     */
    function convertIdToCode($id)
    {
        return str_pad($id, 2, '0', STR_PAD_LEFT);
    }
}
