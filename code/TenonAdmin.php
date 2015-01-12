<?php
/**
 * Provides CMS Administration of Tenon results
 *
 * @package silverstripe-tenon
 * @author josh@novaweb.co.nz
 */
class TenonAdmin extends ModelAdmin {

    private static $managed_models = array(
        'TenonResult',
    );

    private static $menu_icon = 'tenon/images/tenon_rev.png';
    private static $menu_priority = -0.4;
    private static $menu_title = 'Tenon Results';
    private static $url_segment = 'tenon';

}
