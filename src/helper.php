<?php
if (!function_exists('addon_url')) {
    function addon_url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        return Url::build($url, $vars, $suffix, $domain);
    }
}