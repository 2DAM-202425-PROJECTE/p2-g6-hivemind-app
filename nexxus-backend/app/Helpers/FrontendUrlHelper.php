<?php

// app/Helpers/FrontendUrlHelper.php
if (!function_exists('frontend_url')) {
    function frontend_url($path = '', $parameters = [], $secure = null)
    {
        $url = rtrim(config('app.frontend_url'), '/') . '/' . ltrim($path, '/');

        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }

        return $url;
    }
}
