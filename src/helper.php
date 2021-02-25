<?php
if (!function_exists('addon_url')) {
    function addon_url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        return drcms5\addon\util\AddonUrl::build($url, $vars, $suffix, $domain);
    }
}