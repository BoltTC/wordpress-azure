<?php
/**
 * Adds theme specific filters for one click installer module
 *
 * @package Lambda
 * @subpackage Admin
 *
 * @copyright (c) 2015 Oxygenna.com
 * @license **LICENSE**
 * @version 1.53.1
 * @author Oxygenna.com
 */

/*************************************************
    IMPORT THEME FUNCTIONS
*************************************************/

/**
 * Modifies post data to use new imported ids
 *
 * @return void
 * @author
 **/
function oxy_one_click_before_insert_post($post, $one_click)
{
    if (!class_exists('simple_html_dom')) {
        require_once OXY_THEME_DIR . 'vendor/oxygenna/oxygenna-one-click/inc/simple_html_dom.php';
    }

    // create post object
    $post_object = new stdClass();
    // strip slashes added by json
    $post_object->post_content = stripslashes($post['post_content']);

    $gallery_shortcode = oxy_get_content_shortcode($post_object, 'gallery');
    if ($gallery_shortcode !== null) {
        if (isset($gallery_shortcode[0])) {
            // show gallery
            $gallery_ids = null;
            if (array_key_exists(3, $gallery_shortcode)) {
                if (array_key_exists(0, $gallery_shortcode[3])) {
                    $gallery_attrs = shortcode_parse_atts($gallery_shortcode[3][0]);
                    if (array_key_exists('ids', $gallery_attrs)) {
                        // we have a gallery with ids so lets replace the ids
                        $gallery_ids = explode(',', $gallery_attrs['ids']);
                        $new_gallery_ids = array();
                        foreach ($gallery_ids as $gallery_id) {
                            $new_gallery_ids[] = $one_click->install_package->lookup_map('attachments', $gallery_id);
                        }
                        // replace old ids with new ones
                        $old_string = 'ids="' . implode(',', $gallery_ids) . '"';
                        $new_string = 'ids="' . implode(',', $new_gallery_ids) . '"';
                        $post_object->post_content = str_replace($old_string, $new_string, $post_object->post_content);
                    }
                }
            }
        }
    }

    if (!empty($post_object->post_content)) {
        $html = str_get_html($post_object->post_content);
        $imgs = $html->find('img');
        foreach ($imgs as $img) {
            $replace_image_src = $one_click->install_package->lookup_map('images', $img->src);
            if (false !== $replace_image_src) {
                $img->src = $replace_image_src;
            }
        }
        $post_object->post_content = $html->save();

        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'vc_single_image', 'image', 'attachments');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'vc_row', 'background_image', 'attachments');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'shapedimage', 'image', 'attachments');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'staff_featured', 'member', 'oxy_staff');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'post_featured', 'featured', 'post');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'pricing_item', 'image', 'attachments');
        // replace all contact form 7 ids
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'contact-form', 'contact_form', 'wpcf7_contact_form');
        $post_object->post_content = $one_click->replace_shortcode_attachment_id($post_object->post_content, 'contact-form-7', 'id', 'wpcf7_contact_form');
    }

    // replace post content with one from object
    $post['post_content'] = $post_object->post_content;

    return $post;
}
add_filter('oxy_one_click_before_insert_post', 'oxy_one_click_before_insert_post', 10, 2);

/**
 * Modifies imported menu befor save in one click importer
 *
 * @return void
 * @author
 **/
function oxy_one_click_before_wp_update_nav_menu_item($new_menu_item, $menu_item, $one_click)
{
    switch ($menu_item['type']) {
        case 'post_type':
        case 'taxonomy':
            switch ($menu_item['object']) {
                case 'oxy_mega_menu':
                    $mega_menu = get_page_by_title('Mega Menu', 'OBJECT', 'oxy_mega_menu');
                    $new_menu_item['menu-item-object-id'] = $mega_menu->ID;
                    break;
                case 'oxy_mega_columns':
                    $columns = get_posts(array(
                        'post_type' => 'oxy_mega_columns'
                    ));
                    foreach ($columns as $column) {
                        if ($column->post_content === $menu_item['post_content']) {
                            $new_menu_item['menu-item-object-id'] = $column->ID;
                        }
                    }
                    break;
                default:
                    $new_id = $one_click->install_package->lookup_map($menu_item['object'], $menu_item['object_id']);
                    if ($new_id !== false) {
                        $new_menu_item['menu-item-object-id'] = $new_id;
                    }
                    break;
            }
            break;
        case 'custom':
        default:
            // do nothing
            break;
    }
    return $new_menu_item;
}
add_filter('oxy_one_click_before_wp_update_nav_menu_item', 'oxy_one_click_before_wp_update_nav_menu_item', 10, 3);



/**
 * Returns the theme demo content packages
 *
 * @return void
 * @author
 **/
function oxy_filter_import_packages($packages)
{
    return array(
        array(
            'id'           => THEME_SHORT . '-corporate',
            'name'         => __('Corporate', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/corporate/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/corporate/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/corporate/screenshot.jpg',
            'description'  => __('The corporate template is built for business. This will install a clean business style content to make your business stand out from the crowd.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/corporate/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
            ),
        ),
        array(
            'id'           => THEME_SHORT . '-landing',
            'name'         => __('App Landing', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/landing/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/landing/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/landing/screenshot.jpg',
            'description'  => __('Your app is the next big thing.  Let the people know about it with this stylish app landing page.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/landing/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
            )
        ),
        array(
            'id'           => THEME_SHORT . '-shop',
            'name'         => __('Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/shop/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/shop/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/shop/screenshot.jpg',
            'description'  => __('WooCommerce ready shop, this template installs some dummy products as well as some example pages. Perfect for starting your online business.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/shop/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woo Commerce Plugin', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            ),
        ),
        array(
            'id'           => THEME_SHORT . '-journal',
            'name'         => __('Personal', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/personal/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/personal/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/personal/screenshot.jpg',
            'description'  => __('Get your name out there and show the world what you can do.  Personal site to show off your skills and get work.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/personal/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
            )
        ),
        array(
            'id'           => THEME_SHORT . '-blog',
            'name'         => __('Blog', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/journal/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/blog/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/blog/screenshot.jpg',
            'description'  => __('A writers dream.  Focused on readability.  Show of your blogging skills with style.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/blog/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-creative',
            'name'         => __('Creative', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/creative/',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/creative/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/creative/screenshot.jpg',
            'description'  => __('Creative business?  This is the template for you.  Install and show the world your skills.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/creative/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
            )
        ),
        array(
            'id'           => THEME_SHORT . '-restaurant',
            'name'         => __('Restaurant', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/restaurant',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/restaurant/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/restaurant/screenshot.jpg',
            'description'  => __('Hungry?  This restaurant template site is a feast for your eyes.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/restaurant/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Contact Form 7 Datepicker', 'lambda-admin-td'),
                    'path' => 'contact-form-7-datepicker/contact-form-7-datepicker.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-hotel',
            'name'         => __('Hotel', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/hotel',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/hotel/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/hotel/screenshot.jpg',
            'description'  => __('Welcome to the Lambda hotel. The Dom Pérignon is on ice and we have your usual room ready.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/hotel/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Contact Form 7 Datepicker', 'lambda-admin-td'),
                    'path' => 'contact-form-7-datepicker/contact-form-7-datepicker.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-magazine',
            'name'         => __('Magazine', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/magazine',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/magazine/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/magazine/screenshot.jpg',
            'description'  => __('Read all about it!  Lambda releases new magazine template.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/magazine/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-charity',
            'name'         => __('Charity', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/charity',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/charity/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/charity/screenshot.jpg',
            'description'  => __('Generate as much money as possible for charity with this slick looking template.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/charity/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-coming',
            'name'         => __('Coming Soon Page', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/coming-soon',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/coming/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/coming/screenshot.jpg',
            'description'  => __('Use this stunning coming soon page to announce the imminent arrival of your site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/coming/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-yoga',
            'name'         => __('Yoga', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/yoga',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/yoga/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/yoga/screenshot.jpg',
            'description'  => __('Relax, take a deep breath and exhale.  Feel your limbs relax and gaze at this beautiful yoga demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/yoga/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-wedding',
            'name'         => __('Wedding', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/wedding',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/wedding/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/wedding/screenshot.jpg',
            'description'  => __('Do you take this wedding demo to be your content for as long as you shall live?', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/wedding/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-photography',
            'name'         => __('Photography', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/photography',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/photography/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/photography/screenshot.jpg',
            'description'  => __('Show off your finest photography skills with this amazing photography demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/photography/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-medical',
            'name'         => __('Medical', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/medical',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/medical/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/medical/screenshot.jpg',
            'description'  => __('Clean medcal demo for hospitals or pharmaceuticals.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/medical/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Contact Form 7 Datepicker', 'lambda-admin-td'),
                    'path' => 'contact-form-7-datepicker/contact-form-7-datepicker.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-digital-agency',
            'name'         => __('Digital Agency', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/digital-agency',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/digital-agency/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/digital-agency/screenshot.jpg',
            'description'  => __('Your hip new digital agency will look great with this demo site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/digital-agency/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-construction',
            'name'         => __('Construction', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/construction',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/construction/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/construction/screenshot.jpg',
            'description'  => __('Ideal for any construction / building company site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/construction/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-pet',
            'name'         => __('Pets', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/pet',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/pet/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/pet/screenshot.jpg',
            'description'  => __('Any site that loves animals will adore this great pet demo site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/pet/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-education',
            'name'         => __('Education', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/education',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/education/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/education/screenshot.jpg',
            'description'  => __('Teach the world anything with this great education demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/education/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-travel-agency',
            'name'         => __('Travel Agency', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/travel-agency',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/travel-agency/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/travel-agency/screenshot.jpg',
            'description'  => __('Create your own travel agency with this stunning demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/travel-agency/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-law-company',
            'name'         => __('Law Company', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/law-company',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/law-company/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/law-company/screenshot.jpg',
            'description'  => __('In trouble?  Better call saul!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/law-company/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-political',
            'name'         => __('Politcal', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/political',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/political/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/political/screenshot.jpg',
            'description'  => __('What do we want?  A political website.  When do we want it? Now!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/political/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-barber-shop',
            'name'         => __('Barber Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/barber-shop',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/barber-shop/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/barber-shop/screenshot.jpg',
            'description'  => __('Something for the weekend sir?', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/barber-shop/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Contact Form 7 Datepicker', 'lambda-admin-td'),
                    'path' => 'contact-form-7-datepicker/contact-form-7-datepicker.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-architecture',
            'name'         => __('Architecture', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/architecture',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/architecture/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/architecture/screenshot.jpg',
            'description'  => __('Architecture should speak of its time and place, but yearn for timelessness.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/architecture/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-dance-school',
            'name'         => __('Dance School', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/dance-school',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/dance-school/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/dance-school/screenshot.jpg',
            'description'  => __('When you dance, your purpose is not to get to a certain place on the floor. It\'s to enjoy each step along the way.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/dance-school/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-winery',
            'name'         => __('Winery', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/winery',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/winery/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/winery/screenshot.jpg',
            'description'  => __('In Vino Veritas', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/winery/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-gym',
            'name'         => __('Gym', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/gym',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/gym/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/gym/screenshot.jpg',
            'description'  => __('Time to work out and get fit!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/gym/',
            'importFile'   => 'importcf7.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-recipies',
            'name'         => __('Recipies', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/recipies',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/recipies/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/recipies/screenshot.jpg',
            'description'  => __('Mmmm Yummy food', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/recipies/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-single-product',
            'name'         => __('Single Product', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/single-product',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/single-product/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/single-product/screenshot.jpg',
            'description'  => __('Shop template for shops that sell one main item.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/single-product/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-church',
            'name'         => __('Church', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/church',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/church/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/church/screenshot.jpg',
            'description'  => __('Have faith in God... and Lambda.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/church/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => esc_html__('Sermon Manager', 'lambda-admin-td'),
                    'path' => 'sermon-manager-for-wordpress/sermons.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-gadget',
            'name'         => __('Gadget Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/gadget',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/gadget/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/gadget/screenshot.jpg',
            'description'  => __('A shop for all your gadget needs.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/gadget/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-photostudio',
            'name'         => __('Photo Studio', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/photostudio',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/photostudio/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/photostudio/screenshot.jpg',
            'description'  => __('Show off your photography skills with this demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/photostudio/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-hosting',
            'name'         => __('Hosting Company', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/hosting',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/hosting/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/hosting/screenshot.jpg',
            'description'  => __('Set up your own hosting company with this great demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/hosting/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-cv',
            'name'         => __('Curriculum Vitae', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/cv',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/cv/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/cv/screenshot.jpg',
            'description'  => __('Advertize your skils online.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/cv/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-startup',
            'name'         => __('Web Startup', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/startup',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/startup/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/startup/screenshot.jpg',
            'description'  => __('Get your startup started!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/startup/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-performing-art',
            'name'         => __('Performing Art', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/performing-art',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/performing-art/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/performing-art/screenshot.jpg',
            'description'  => __('Music, Dance, Opera Theater demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/performing-art/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-fashion-magazine',
            'name'         => __('Fashion Magazine', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/fashion-magazine',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/fashion-magazine/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/fashion-magazine/screenshot.jpg',
            'description'  => __('Fashion! Turn to the left.  Fashion! Turn to the right.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/fashion-magazine/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-brewery',
            'name'         => __('Brewery Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/brewery',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/brewery/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/brewery/screenshot.jpg',
            'description'  => __('Mmmmm Beeeer - Homer Simpson', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/brewery/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-tattoo',
            'name'         => __('Tattoo Parlor', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/tattoo',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/tattoo/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/tattoo/screenshot.jpg',
            'description'  => __('Get Inked', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/tattoo/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-organic',
            'name'         => __('Organic Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/organic',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/organic/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/organic/screenshot.jpg',
            'description'  => __('Fresh organic produce.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/organic/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-music-band',
            'name'         => __('Music Band', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/music-band',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/music-band/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/music-band/screenshot.jpg',
            'description'  => __('Get the band back together and make a web site to promote it.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/music-band/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-logistics',
            'name'         => __('Logistics', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/logistics',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/logistics/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/logistics/screenshot.jpg',
            'description'  => __('Expand your logistics business.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/logistics/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-martialarts',
            'name'         => __('Martial Arts', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/martialarts',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/martialarts/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/martialarts/screenshot.jpg',
            'description'  => __('Everybody was kung fu fighting, ha woo cha!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/martialarts/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-handyman',
            'name'         => __('Handyman', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/handyman',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/handyman/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/handyman/screenshot.jpg',
            'description'  => __('Mr Fixit.  He can fix anything', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/handyman/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-cafe',
            'name'         => __('Cafe', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/cafe',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/cafe/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/cafe/screenshot.jpg',
            'description'  => __('Coffee Break, take it easy and relax in our city cafe.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/cafe/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-product',
            'name'         => __('Product Launch', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/product',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/product/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/product/screenshot.jpg',
            'description'  => __('Coffee Break, take it easy and relax in our city product.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/product/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-subpackage',
            'name'         => __('Spa Beauty', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/spa',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/spa/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/spa/screenshot.jpg',
            'description'  => __('Relax and take a break at our luxurious spa.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/spa/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-gardening',
            'name'         => __('Gardening', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/gardening',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/gardening/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/gardening/screenshot.jpg',
            'description'  => __('Relax and take a break at our luxurious gardening.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/gardening/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-pizza',
            'name'         => __('Pizza', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/pizza',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/pizza/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/pizza/screenshot.jpg',
            'description'  => __('Fresh from the oven.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/pizza/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-senior',
            'name'         => __('Senior Care', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/senior',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/senior/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/senior/screenshot.jpg',
            'description'  => __('To care for those who once cared for us is one of the highest honors.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/senior/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-consultant',
            'name'         => __('Consultant', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/consultant',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/consultant/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/consultant/screenshot.jpg',
            'description'  => __('Professional looking consultancy site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/consultant/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-extreme',
            'name'         => __('Extreme Sports', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/extreme',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/extreme/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/extreme/screenshot.jpg',
            'description'  => __('Get extreme thrills with this demo!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/extreme/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-awards',
            'name'         => __('Awards Site', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/awards',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/awards/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/awards/screenshot.jpg',
            'description'  => __('And the award for best WP theme goes to.... Lambda!', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/awards/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-horse',
            'name'         => __('Horse Riding', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/horse',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/horse/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/horse/screenshot.jpg',
            'description'  => __('Woooh there, thats a fine horse riding site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/horse/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-aquarium',
            'name'         => __('Aquarium', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/aquarium',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/aquarium/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/aquarium/screenshot.jpg',
            'description'  => __('Video header themed aquarium site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/aquarium/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-business-rtl',
            'name'         => __('Business RTL', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/business-rtl',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/business-rtl/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/business-rtl/screenshot.jpg',
            'description'  => __('Get your business off to a good start with this RTL demo.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/business-rtl/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-hipster',
            'name'         => __('Hipster Fashion Shop', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/hipster',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/hipster/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/hipster/screenshot.jpg',
            'description'  => __('Hey there hipster.  Looking good.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/hipster/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                ),
                array(
                    'name' => __('Woocommerce', 'lambda-admin-td'),
                    'path' => 'woocommerce/woocommerce.php'
                ),
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-landing-app',
            'name'         => __('App Landing', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/landing-app',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/landing-app/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/landing-app/screenshot.jpg',
            'description'  => __('Show off your new app with this amazing site.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/landing-app/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-games',
            'name'         => __('Gamer Blog', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/games',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/games/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/games/screenshot.jpg',
            'description'  => __('Talk about games and stuff.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/games/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Revolution Slider', 'lambda-admin-td'),
                    'path' => 'revslider/revslider.php'
                ),
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-taxi',
            'name'         => __('Taxi Service', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/taxi',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/taxi/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/taxi/screenshot.jpg',
            'description'  => __('Talk about taxi and stuff.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/taxi/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-doctor',
            'name'         => __('Doctors Surgery', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/doctor',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/doctor/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/doctor/screenshot.jpg',
            'description'  => __('What seems to be the problem now?', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/doctor/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-tourist-destination',
            'name'         => __('Tourist Destination', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/tourist-destination',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/tourist-destination/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/tourist-destination/screenshot.jpg',
            'description'  => __('Show off a fantastic place to go for a vacation.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/tourist-destination/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        ),
        array(
            'id'           => THEME_SHORT . '-elearning',
            'name'         => __('E-Learning Site', 'lambda-admin-td'),
            'demo_url'     => 'http://lambda.oxygenna.com/elearning',
            'docs_url'     => 'http://help.oxygenna.com/wordpress/lambda',
            'thumbnail'    => 'http://one-click-import.s3.amazonaws.com/lambda/elearning/thumbnail.jpg',
            'screenshot'   => 'http://one-click-import.s3.amazonaws.com/lambda/elearning/screenshot.jpg',
            'description'  => __('Feed the mind.', 'lambda-admin-td'),
            'type'         => 'oxygenna',
            'importUrl'    => 'https://one-click-import.s3.amazonaws.com/lambda/elearning/',
            'importFile'   => 'import.json',
            'requirements' => array(
                array(
                    'name' => __('Contact Form 7', 'lambda-admin-td'),
                    'path' => 'contact-form-7/wp-contact-form-7.php'
                ),
                array(
                    'name' => __('Visual Composer Plugin', 'lambda-admin-td'),
                    'path' => 'js_composer/js_composer.php'
                )
            )
        )
    );
}
add_filter('oxy_one_click_import_packages', 'oxy_filter_import_packages', 10, 1);

/**
 * Adds extra custom fields to menus
 *
 * @return void
 * @author
 **/
function oxy_one_click_import_add_metadata_menu_item($new_menu_item_id, $menu_item, $one_click)
{
    // add custom data if exists
    if (isset($menu_item['custom_fields'])) {
        foreach ($menu_item['custom_fields'] as $key => $custom_field) {
            // just import oxygenna fields
            if (strpos($key, 'oxy_') !== false) {
                switch ($key) {
                    case 'oxy_bg_url':
                        $new_image = $one_click->install_package->lookup_map('images', $custom_field[0]);
                        add_post_meta($new_menu_item_id, $key, $new_image);
                        break;
                    default:
                        add_post_meta($new_menu_item_id, $key, $custom_field[0]);

                        break;
                }
            }
        }
    }
}
add_action('oxy_one_click_new_menu_item', 'oxy_one_click_import_add_metadata_menu_item', 10, 3);
/**
 * Does final setup tasks at the end of the import
 *
 * @return void
 * @author
 **/
function oxy_one_click_final_setup($data, $OneClick)
{
    global $oxy_theme;

    // install page ids with a look up to see what is the new id
    if (isset($data['page_options'])) {
        foreach ($data['page_options'] as $option => $option_value) {
            update_option($option, $OneClick->install_package->lookup_map('page', $option_value));
        }
    }

    $OneClick->install_package->add_log_message('Set Page Options');

    // now save the regular options
    if (isset($data['options'])) {
        foreach ($data['options'] as $option => $option_value) {
            update_option($option, $option_value);
        }
    }

    // set up theme_mods if we have any
    if (isset($data['theme_mods'])) {
        foreach ($data['theme_mods'] as $name => $value) {
            set_theme_mod($name, $value);
        }
    }

    // set up theme options
    if (isset($data['theme_options'])) {
        foreach ($data['theme_options'] as $id => $value) {
            $new_value = null;
            switch ($id) {
                case '404_page':
                case 'portfolio_page':
                case 'portfolio_archive_page':
                case 'services_archive_page':
                case 'staff_archive_page':
                    $new_id = $OneClick->install_package->lookup_map('pages', $value);
                    if (false !== $new_id) {
                        $new_value = $new_id;
                    }
                    break;
                case 'site_stack':
                    $new_id = $OneClick->install_package->lookup_map('oxy_stack', $value);
                    if (false !== $new_id) {
                        $new_value = $new_id;
                    }
                    // save new css to file
                    if (!class_exists('OxygennaStacks')) {
                        require_once(OXY_STACKS_DIR . 'inc/OxygennaStacks.php');
                    }
                    // get stack instance and save the meta data to the file
                    $OxyStack = OxygennaStacks::instance();
                    $OxyStack->update_css_in_file($new_value);
                    break;
                case 'logo_image':
                case 'logo_image_trans':
                    if (!empty($value)) {
                        $new_url = $OneClick->install_package->lookup_map('images', $value);
                        if (!empty($new_url)) {
                            $new_value = $new_url;
                        }
                    } else {
                        $new_value = '';
                    }
                    break;
                case 'favicon':
                case 'iphone_icon':
                case 'iphone_retina_icon':
                case 'ipad_icon':
                case 'ipad_icon_retina':
                case 'google_anal':
                case 'one_click_throttle':
                    // do nothing
                    break;
                default:
                    $new_value = $value;
                    break;
            }
            if (null !== $new_value) {
                $oxy_theme->set_option($id, $new_value);
            }
        }
    }
}
add_action('oxy_one_click_final_setup', 'oxy_one_click_final_setup', 10, 2);

/*************************************************
    EXPORT FUNCTIONS
*************************************************/

/**
 * Adds the skin post to the end of the export array
 *
 * @return void
 * @author
 **/
function oxy_add_skin_to_export($export, $OxyExport)
{
    // get current skin that is set in customiser
    global $oxy_theme;
    $site_stack = $oxy_theme->get_option('site_stack');

    // fetch the skin post
    $skin = get_post($site_stack);
    if (null !== $skin) {
        // export the post and add it to the export posts array
        $export['posts'][] = $OxyExport->export_post($skin);
    }

    return $export;
}
add_filter('oxy_export_filter_export', 'oxy_add_skin_to_export', 10, 2);

/**
 * Adds final options to export data structure
 *
 * @return void
 * @author
 **/
function oxy_export_filter_export($export)
{
    $theme_options = get_option(THEME_SHORT . '-options');

    global $oxy_theme;
    $export['final_setup'] = array(
        'page_options' => array(
            'page_for_posts' => get_option('page_for_posts'),
            'page_on_front' => get_option('page_on_front'),
        ),
        'options' => array(
            'show_on_front' => get_option('show_on_front'),
        ),
        'theme_mods' => array(
            'background_color' => get_theme_mod('background_color'),
            'background_image' => get_theme_mod('background_image')
        ),
        'theme_options' => apply_filters('oxy-export-theme-options', $theme_options),
    );

    if (is_plugin_active('woocommerce/woocommerce.php')) {
        $woocommerce_option = array('shop', 'cart', 'checkout', 'myaccount');
        foreach ($woocommerce_option as $option) {
            $option = 'woocommerce_' . $option . '_page_id';
            if (isset($export['final_setup']['page_options'][$option])) {
                $export['final_setup']['page_options'][$option] = get_option($option);
            }
        }
    }

    return $export;
}
add_filter('oxy_export_filter_export', 'oxy_export_filter_export', 10, 1);

/**
 * Pre export function - need to save the stack to the metadata (in case being saved in a file)
 *
 * @return void
 * @author
 **/
function oxy_save_stack_before_export()
{
    global $oxy_theme;
    $site_stack_id = $oxy_theme->get_option('site_stack');
    $settings_options = array(
        'css_save_to' => 'header',
        'css_format'  => 'scss_formatter_compressed'
    );
    $oxygenna_stack = OxygennaStacks::instance();
    $oxygenna_stack->save_post_css($site_stack_id, $settings_options);
}
add_action('oxy_export_pre_export', 'oxy_save_stack_before_export');

/**
 * Create check list for one click installer
 *
 * @return void
 * @author
 **/
function oxy_one_click_checklist()
{
    // get packages so we can get url to test
    $packages = oxy_filter_import_packages(array());

    return array(
        array(
            'name' => 'WPMemoryCheck',
            'args' => array(
                'limit' => '40M'
            )
        ),
        array(
            'name' => 'MaxExecTime',
            'args' => array(
                'value' => 30,
            )
        ),
        array(
            'name' => 'FSockCheck',
            'args' => array()
        ),
        array(
            'name' => 'DNSCheck',
            'args' => array(
                'domain' => 'google.com'
            )
        ),
        // use first package as a test url
        array(
            'name' => 'OutConnectCheck',
            'args' => array(
                'domain' => $packages[0]['importUrl'] . $packages[0]['importFile']
            )
        ),
        array(
            'name' => 'ZipCheck',
            'args' => array(
                'name'  => 'PHP Zip Archive',
                'value' => 'ZipArchive',
                'ok_message' => __('Your server has PHP Zip or unzip_file enabled. Revolution Slider import will work.', 'lambda-admin-td'),
                'fail_message' => __('Your server does not have PHP Zip enabled or unzip_file function - Revolution Slider slides will not be able to be unpacked. Contact your hosting provider.', 'lambda-admin-td')
            )
        )
    );
}
add_filter('oxy_one_click_checklist', 'oxy_one_click_checklist', 10, 1);

/**
 * Set plugins url for the one click installer
 *
 * @return void
 * @author
 **/
function oxy_one_click_details()
{
    return array(
        'install_plugins_url' => esc_url(
            add_query_arg(
                array(
                    'page'   => 'tgmpa-install-plugins'
                ),
                admin_url('themes.php')
            )
        )
    );
}
add_filter('oxy_one_click_details', 'oxy_one_click_details', 10, 1);
