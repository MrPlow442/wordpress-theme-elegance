<?php

define('ELEGANCE_NAV_META_KEY', '_elegance_nav_type');
define('ELEGANCE_NAV_ID_KEY', '_elegance_nav_id');

enum EleganceNavType: string {
    case LINK = 'link';
    case ANCHOR = 'anchor';
}