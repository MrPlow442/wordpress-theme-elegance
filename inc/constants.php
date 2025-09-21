<?php

define('ELEGANCE_NAV_META_KEY', '_elegance_nav_type');
define('ELEGANCE_NAV_ID_KEY', '_elegance_nav_id');

class EleganceNavType {
    const LINK = 'link';
    const ANCHOR = 'anchor';
}

class EleganceNavId {
    const HOME = 'elegance_nav_home_nav';
    const NOTICES = 'elegance_nav_notices_nav';
    const BLOG = 'elegance_nav_blog_nav';
    const TESTIMONIALS = 'elegance_nav_testimonials_nav';
}

class EleganceBlogAvatarSize {
    const LARGE = 48;
    const SMALL = 32;
}