<?php
/**
 * Plugin Name: HvP Iran Cities Dropdown
 * Plugin URI:  https://github.com/HvPWorks/Iran-cities-dropdown
 * Description: نمایش داینامیک فیلد شهرها بر اساس استان انتخابی در ووکامرس و مرتب سازی فیلد استان ووکامرس بر اساس حروف الفبا(در بخش های پرداخت ، حساب کاربری ، حمل نقل)  
 * Version: 1.0
 * Author: HvP Works
 * Author URI:        https://t.me/hvpworks
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Copyright (C) 2025 HvP
 */


if ( ! defined( 'ABSPATH' ) ) exit;

// نمایش نام کامل استان به جای کد در فاکتور، سفارش و ایمیل‌ها
add_filter('woocommerce_order_formatted_billing_address', 'hvp_replace_state_code_with_name', 10, 2);
add_filter('woocommerce_order_formatted_shipping_address', 'hvp_replace_state_code_with_name', 10, 2);

function hvp_replace_state_code_with_name($address, $order) {
    $states = WC()->countries->get_states('IR');

    if (isset($address['state']) && isset($states[$address['state']])) {
        $address['state'] = $states[$address['state']];
    }

    return $address;
}


// لود اسکریپت و انتقال json
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'hvp-iran-cities',
        plugin_dir_url(__FILE__) . 'HvP-Iran-cities-dropdown.js',
        array('jquery'),
        time(), // برای جلوگیری از کش
        true
    );
    
/*    
    add_filter('woocommerce_states', function ($states) {
    if (isset($states['IR'])) {
        $provinces = $states['IR'];

        // مرتب‌سازی بر اساس حروف الفبای فارسی
        uasort($provinces, function ($a, $b) {
            return strcmp($a, $b);
        });

        $states['IR'] = $provinces;
    }
    return $states;
});

*/

// مرتب‌سازی استان‌های ایران بر اساس حروف الفبای فارسی به شکل دقیق
add_filter('woocommerce_states', function ($states) {
    if (isset($states['IR'])) {
        $provinces = $states['IR'];

        // لیست ترتیب حروف فارسی برای مرتب‌سازی دقیق‌تر
        $alphabet = ['آ','ا','ب','پ','ت','ث','ج','چ','ح','خ','د','ذ','ر','ز','ژ','س','ش','ص','ض','ط','ظ','ع','غ','ف','ق','ک','گ','ل','م','ن','و','ه','ی'];

        // تابع برای مرتب‌سازی فارسی
        usort($provinces, function($a, $b) use ($alphabet) {
            $a = trim($a);
            $b = trim($b);
            $len = min(mb_strlen($a, 'UTF-8'), mb_strlen($b, 'UTF-8'));

            for ($i = 0; $i < $len; $i++) {
                $charA = mb_substr($a, $i, 1, 'UTF-8');
                $charB = mb_substr($b, $i, 1, 'UTF-8');

                $posA = array_search($charA, $alphabet);
                $posB = array_search($charB, $alphabet);

                // اگر هر دو در الفبا باشند
                if ($posA !== false && $posB !== false) {
                    if ($posA < $posB) return -1;
                    if ($posA > $posB) return 1;
                } else {
                    // اگر یکی از حروف در لیست نبود، از strcmp استفاده کن
                    $cmp = strcmp($charA, $charB);
                    if ($cmp !== 0) return $cmp;
                }
            }

            return mb_strlen($a, 'UTF-8') - mb_strlen($b, 'UTF-8');
        });

        // چون کلیدها بعد از usort از بین می‌رن، باید دوباره بازسازی کنیم
        $sorted = [];
        foreach ($provinces as $name) {
            $key = array_search($name, $states['IR']);
            if ($key !== false) {
                $sorted[$key] = $name;
            }
        }

        $states['IR'] = $sorted;
    }
    return $states;
});


//******************************
    $counties_path = plugin_dir_path(__FILE__) . 'counties.json';
    if (file_exists($counties_path)) {
        $counties_json = file_get_contents($counties_path);
        $counties = json_decode($counties_json, true);
    } else {
        $counties = [];
    }

    wp_localize_script('hvp-iran-cities', 'iran_data', [
        'counties' => $counties
    ]);
});

// تنظیم فیلد شهر به صورت داینامیک
add_filter('woocommerce_default_address_fields', function($fields) {
    $fields['city']['type'] = 'select';
    $fields['city']['label'] = 'شهر';
    $fields['city']['options'] = array('' => 'لطفاً ابتدا استان را انتخاب کنید');
    $fields['city']['required'] = true;
    $fields['city']['priority'] = 69;
    return $fields;
});
