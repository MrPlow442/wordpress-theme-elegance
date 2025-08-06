<?php

define('ELEGANCE_NAV_META_KEY', '_elegance_nav_type');
define('ELEGANCE_NAV_ID_KEY', '_elegance_nav_id');

enum EleganceNavType: string {
    case LINK = 'link';
    case ANCHOR = 'anchor';
}

enum EleganceNavId: string {
    case HOME = 'elegance_nav_home_nav';
    case NOTICES = 'elegance_nav_notices_nav';
    case BLOG = 'elegance_nav_blog_nav';
    case TESTIMONIALS = 'elegance_nav_testimonials_nav';
}