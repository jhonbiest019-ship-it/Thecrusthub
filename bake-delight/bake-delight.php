<?php
/**
 * Plugin Name: Bake Delight Professional Bakery Manager
 * Description: Standalone-feel bakery management system with custom storefront, vendor dashboard, and WhatsApp checkout.
 * Version: 2.0.0
 * Author: Sikandar Hayat Baba
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BakeDelightPlugin')) {
    class BakeDelightPlugin
    {
        const VERSION = '2.0.0';
        const NONCE_ADMIN = 'bd_admin_nonce';
        const NONCE_STORE = 'bd_store_nonce';

        public function __construct()
        {
            register_activation_hook(__FILE__, array($this, 'activate'));
            add_shortcode('bake_delight_admin', array($this, 'admin_shortcode'));
            add_shortcode('bake_delight_store', array($this, 'store_shortcode'));
            add_action('template_redirect', array($this, 'template_redirect_handler'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
            add_action('wp_ajax_bd_admin_action', array($this, 'admin_ajax'));
            add_action('wp_ajax_bd_store_action', array($this, 'store_ajax'));
            add_action('wp_ajax_nopriv_bd_store_action', array($this, 'store_ajax'));
        }

        public function activate()
        {
            global $wpdb;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $charset = $wpdb->get_charset_collate();
            $cat_table = $wpdb->prefix . 'bd_categories';
            $prod_table = $wpdb->prefix . 'bd_products';
            $order_table = $wpdb->prefix . 'bd_orders_log';
            $settings_table = $wpdb->prefix . 'bd_settings';

            dbDelta("CREATE TABLE {$cat_table} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(191) NOT NULL,
                slug VARCHAR(191) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY slug (slug)
            ) {$charset};");

            dbDelta("CREATE TABLE {$prod_table} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                cat_id BIGINT(20) UNSIGNED NOT NULL,
                title VARCHAR(191) NOT NULL,
                description TEXT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                weight_options LONGTEXT NULL,
                image_url TEXT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'live',
                stock_status VARCHAR(30) NOT NULL DEFAULT 'in_stock',
                PRIMARY KEY (id),
                KEY cat_id (cat_id)
            ) {$charset};");

            dbDelta("CREATE TABLE {$order_table} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                customer_name VARCHAR(191) NOT NULL,
                details LONGTEXT NOT NULL,
                total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                delivery_time DATETIME NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'logged',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY created_at (created_at)
            ) {$charset};");

            dbDelta("CREATE TABLE {$settings_table} (
                setting_key VARCHAR(191) NOT NULL,
                setting_value LONGTEXT NOT NULL,
                PRIMARY KEY (setting_key)
            ) {$charset};");

            $this->upsert_setting('whatsapp_number', '');
            $this->upsert_setting('lead_time_hours', '24');
            $this->upsert_setting('currency', 'PKR');

            if (!get_page_by_path('bake-admin')) {
                wp_insert_post(array(
                    'post_title' => 'Bake Admin Dashboard',
                    'post_name' => 'bake-admin',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => '[bake_delight_admin]',
                ));
            }
            if (!get_page_by_path('bake-store')) {
                wp_insert_post(array(
                    'post_title' => 'Online Storefront',
                    'post_name' => 'bake-store',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => '[bake_delight_store]',
                ));
            }
        }

        public function enqueue_assets()
        {
            if (!(is_page('bake-admin') || is_page('bake-store'))) {
                return;
            }

            wp_enqueue_script('jquery');
            wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true);
            wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13');

            wp_register_script('bd-inline-js', false, array('jquery'), self::VERSION, true);
            wp_enqueue_script('bd-inline-js');
            wp_localize_script('bd-inline-js', 'BakeDelightData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'admin_nonce' => wp_create_nonce(self::NONCE_ADMIN),
                'store_nonce' => wp_create_nonce(self::NONCE_STORE),
                'lead_time_hours' => intval($this->get_setting('lead_time_hours', 24)),
                'currency' => $this->get_setting('currency', 'PKR'),
                'whatsapp' => $this->get_setting('whatsapp_number', ''),
            ));
            wp_add_inline_script('bd-inline-js', $this->inline_js());

            wp_register_style('bd-inline-css', false, array(), self::VERSION);
            wp_enqueue_style('bd-inline-css');
            wp_add_inline_style('bd-inline-css', $this->inline_css());
        }

        public function template_redirect_handler()
        {
            if (is_page('bake-admin')) {
                if (!current_user_can('manage_options')) {
                    wp_die(esc_html__('Access denied.', 'bake-delight'));
                }
                status_header(200);
                echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
                wp_head();
                echo '</head><body class="bd-page bd-admin-page">';
                echo do_shortcode('[bake_delight_admin]');
                wp_footer();
                echo '</body></html>';
                exit;
            }

            if (is_page('bake-store')) {
                status_header(200);
                echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
                wp_head();
                echo '</head><body class="bd-page bd-store-page">';
                echo do_shortcode('[bake_delight_store]');
                wp_footer();
                echo '</body></html>';
                exit;
            }
        }

        public function admin_shortcode()
        {
            if (!current_user_can('manage_options')) {
                return '<div class="bd-wrap"><div class="bd-card">Access denied.</div></div>';
            }

            ob_start();
            ?>
            <div class="bd-wrap">
                <aside class="bd-sidebar">
                    <h2>Bake Delight</h2>
                    <nav>
                        <button class="bd-nav active" data-panel="products" type="button">Product Manager</button>
                        <button class="bd-nav" data-panel="categories" type="button">Category Manager</button>
                        <button class="bd-nav" data-panel="orders" type="button">Order Analytics</button>
                        <button class="bd-nav" data-panel="settings" type="button">Store Settings</button>
                    </nav>
                </aside>
                <main class="bd-main">
                    <section class="bd-panel active" id="bd-panel-products">
                        <div class="bd-card">
                            <h3>Product Manager</h3>
                            <form id="bd-product-form">
                                <?php wp_nonce_field(self::NONCE_ADMIN, 'bd_admin_nonce_field'); ?>
                                <input type="hidden" name="product_id" value="">
                                <input type="text" name="title" placeholder="Product title" required>
                                <textarea name="description" placeholder="Description"></textarea>
                                <div class="bd-grid-2">
                                    <input type="number" step="0.01" name="price" placeholder="Price" required>
                                    <input type="text" name="weight_options" placeholder='Weight JSON e.g. ["0.5kg","1kg"]'>
                                </div>
                                <div class="bd-grid-2">
                                    <input type="text" name="image_url" placeholder="Image URL">
                                    <select name="cat_id" id="bd-product-category"></select>
                                </div>
                                <div class="bd-grid-2">
                                    <select name="status">
                                        <option value="live">Live</option>
                                        <option value="hidden">Hidden</option>
                                    </select>
                                    <select name="stock_status">
                                        <option value="in_stock">In Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                    </select>
                                </div>
                                <button type="submit">Save Product</button>
                            </form>
                            <div id="bd-products-table"></div>
                        </div>
                    </section>

                    <section class="bd-panel" id="bd-panel-categories">
                        <div class="bd-card">
                            <h3>Category Manager</h3>
                            <form id="bd-category-form">
                                <?php wp_nonce_field(self::NONCE_ADMIN, 'bd_admin_nonce_cat'); ?>
                                <input type="hidden" name="category_id" value="">
                                <input type="text" name="name" placeholder="Category name" required>
                                <button type="submit">Save Category</button>
                            </form>
                            <div id="bd-categories-table"></div>
                        </div>
                    </section>

                    <section class="bd-panel" id="bd-panel-orders">
                        <div class="bd-card">
                            <h3>Order Analytics</h3>
                            <input type="text" id="bd-order-search" placeholder="Search by customer or details">
                            <div id="bd-orders-table"></div>
                        </div>
                    </section>

                    <section class="bd-panel" id="bd-panel-settings">
                        <div class="bd-card">
                            <h3>Store Settings</h3>
                            <form id="bd-settings-form">
                                <?php wp_nonce_field(self::NONCE_ADMIN, 'bd_admin_nonce_set'); ?>
                                <input type="text" name="whatsapp_number" placeholder="WhatsApp Number">
                                <div class="bd-grid-2">
                                    <input type="number" min="24" name="lead_time_hours" placeholder="Lead Time Hours (min 24)">
                                    <input type="text" name="currency" placeholder="Currency e.g. PKR">
                                </div>
                                <button type="submit">Save Settings</button>
                            </form>
                        </div>
                    </section>

                    <footer class="bd-branding">Designed and Developed by Sikandar Hayat Baba</footer>
                </main>
            </div>
            <?php
            return ob_get_clean();
        }

        public function store_shortcode()
        {
            ob_start();
            ?>
            <div class="bd-store-wrap">
                <header class="bd-store-header">
                    <h1>Bake Delight</h1>
                    <p>Luxury home-baked treats crafted with love.</p>
                </header>

                <section class="bd-store-controls bd-card">
                    <div id="bd-store-categories"></div>
                </section>

                <section id="bd-store-grid" class="bd-store-grid"></section>

                <section class="bd-card bd-cart-card">
                    <h3>Your Cart</h3>
                    <div id="bd-cart-items"></div>
                    <div class="bd-cart-total">Total: <span id="bd-cart-total-value">0</span></div>
                    <form id="bd-checkout-form">
                        <?php wp_nonce_field(self::NONCE_STORE, 'bd_store_nonce_field'); ?>
                        <input type="text" name="customer_name" placeholder="Your Name" required>
                        <input type="text" id="bd-delivery-time" name="delivery_time" placeholder="Select Delivery Date/Time" required>
                        <button type="submit">Place Order on WhatsApp</button>
                        <p id="bd-checkout-error" class="bd-error"></p>
                    </form>
                </section>

                <div class="bd-modal" id="bd-quick-view-modal">
                    <div class="bd-modal-backdrop"></div>
                    <div class="bd-modal-card bd-card">
                        <button type="button" class="bd-modal-close" id="bd-modal-close">x</button>
                        <div id="bd-modal-content"></div>
                    </div>
                </div>

                <footer class="bd-branding">Designed and Developed by Sikandar Hayat Baba</footer>
            </div>
            <?php
            return ob_get_clean();
        }

        public function admin_ajax()
        {
            if (!current_user_can('manage_options')) {
                wp_send_json_error(array('message' => 'Unauthorized'));
            }
            check_ajax_referer(self::NONCE_ADMIN, 'nonce');

            $sub = isset($_POST['sub_action']) ? sanitize_text_field(wp_unslash($_POST['sub_action'])) : '';
            global $wpdb;
            $cat_table = $wpdb->prefix . 'bd_categories';
            $prod_table = $wpdb->prefix . 'bd_products';
            $order_table = $wpdb->prefix . 'bd_orders_log';

            switch ($sub) {
                case 'list_all':
                    wp_send_json_success(array(
                        'categories' => $wpdb->get_results("SELECT * FROM {$cat_table} ORDER BY id DESC", ARRAY_A),
                        'products' => $wpdb->get_results("SELECT * FROM {$prod_table} ORDER BY id DESC", ARRAY_A),
                        'orders' => $wpdb->get_results("SELECT * FROM {$order_table} ORDER BY id DESC LIMIT 200", ARRAY_A),
                        'settings' => array(
                            'whatsapp_number' => $this->get_setting('whatsapp_number', ''),
                            'lead_time_hours' => $this->get_setting('lead_time_hours', '24'),
                            'currency' => $this->get_setting('currency', 'PKR'),
                        ),
                    ));
                    break;

                case 'save_category':
                    $id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
                    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
                    if ('' === $name) {
                        wp_send_json_error(array('message' => 'Name required'));
                    }
                    $slug = sanitize_title($name);
                    if ($id > 0) {
                        $wpdb->update($cat_table, array('name' => $name, 'slug' => $slug), array('id' => $id));
                    } else {
                        $wpdb->insert($cat_table, array('name' => $name, 'slug' => $slug));
                    }
                    wp_send_json_success();
                    break;

                case 'delete_category':
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    if ($id > 0) {
                        $wpdb->delete($cat_table, array('id' => $id));
                    }
                    wp_send_json_success();
                    break;

                case 'save_product':
                    $id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
                    $data = array(
                        'cat_id' => isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0,
                        'title' => isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '',
                        'description' => isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '',
                        'price' => isset($_POST['price']) ? floatval($_POST['price']) : 0,
                        'weight_options' => isset($_POST['weight_options']) ? sanitize_text_field(wp_unslash($_POST['weight_options'])) : '[]',
                        'image_url' => isset($_POST['image_url']) ? esc_url_raw(wp_unslash($_POST['image_url'])) : '',
                        'status' => isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'live',
                        'stock_status' => isset($_POST['stock_status']) ? sanitize_text_field(wp_unslash($_POST['stock_status'])) : 'in_stock',
                    );
                    if ($id > 0) {
                        $wpdb->update($prod_table, $data, array('id' => $id));
                    } else {
                        $wpdb->insert($prod_table, $data);
                    }
                    wp_send_json_success();
                    break;

                case 'delete_product':
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    if ($id > 0) {
                        $wpdb->delete($prod_table, array('id' => $id));
                    }
                    wp_send_json_success();
                    break;

                case 'save_settings':
                    $whatsapp = isset($_POST['whatsapp_number']) ? sanitize_text_field(wp_unslash($_POST['whatsapp_number'])) : '';
                    $lead = isset($_POST['lead_time_hours']) ? max(24, intval($_POST['lead_time_hours'])) : 24;
                    $currency = isset($_POST['currency']) ? sanitize_text_field(wp_unslash($_POST['currency'])) : 'PKR';
                    $this->upsert_setting('whatsapp_number', $whatsapp);
                    $this->upsert_setting('lead_time_hours', (string) $lead);
                    $this->upsert_setting('currency', $currency);
                    wp_send_json_success();
                    break;
            }

            wp_send_json_error(array('message' => 'Invalid action'));
        }

        public function store_ajax()
        {
            check_ajax_referer(self::NONCE_STORE, 'nonce');
            $sub = isset($_POST['sub_action']) ? sanitize_text_field(wp_unslash($_POST['sub_action'])) : '';

            global $wpdb;
            $cat_table = $wpdb->prefix . 'bd_categories';
            $prod_table = $wpdb->prefix . 'bd_products';
            $order_table = $wpdb->prefix . 'bd_orders_log';

            if ('list_products' === $sub) {
                $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
                $where = "status='live' AND stock_status='in_stock'";
                if ($cat_id > 0) {
                    $where .= $wpdb->prepare(' AND cat_id = %d', $cat_id);
                }
                wp_send_json_success(array(
                    'products' => $wpdb->get_results("SELECT * FROM {$prod_table} WHERE {$where} ORDER BY id DESC", ARRAY_A),
                    'categories' => $wpdb->get_results("SELECT * FROM {$cat_table} ORDER BY name ASC", ARRAY_A),
                ));
            }

            if ('log_order' === $sub) {
                $name = isset($_POST['customer_name']) ? sanitize_text_field(wp_unslash($_POST['customer_name'])) : '';
                $details = isset($_POST['details']) ? sanitize_textarea_field(wp_unslash($_POST['details'])) : '';
                $total = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
                $delivery = isset($_POST['delivery_time']) ? sanitize_text_field(wp_unslash($_POST['delivery_time'])) : '';

                $delivery_ts = strtotime($delivery);
                $lead = intval($this->get_setting('lead_time_hours', 24));
                if (!$delivery_ts) {
                    wp_send_json_error(array('message' => 'Invalid delivery time'));
                }
                if (($delivery_ts - current_time('timestamp')) < ($lead * HOUR_IN_SECONDS)) {
                    wp_send_json_error(array('message' => 'Order must be placed 24 hours in advance.'));
                }

                $wpdb->insert($order_table, array(
                    'customer_name' => $name,
                    'details' => $details,
                    'total_price' => $total,
                    'delivery_time' => gmdate('Y-m-d H:i:s', $delivery_ts),
                    'status' => 'logged',
                ));

                wp_send_json_success(array(
                    'whatsapp' => $this->get_setting('whatsapp_number', ''),
                ));
            }

            wp_send_json_error(array('message' => 'Invalid action'));
        }

        private function get_setting($key, $default = '')
        {
            global $wpdb;
            $table = $wpdb->prefix . 'bd_settings';
            $value = $wpdb->get_var($wpdb->prepare("SELECT setting_value FROM {$table} WHERE setting_key = %s", $key));
            return null !== $value ? $value : $default;
        }

        private function upsert_setting($key, $value)
        {
            global $wpdb;
            $table = $wpdb->prefix . 'bd_settings';
            $exists = $wpdb->get_var($wpdb->prepare("SELECT setting_key FROM {$table} WHERE setting_key = %s", $key));
            if ($exists) {
                $wpdb->update($table, array('setting_value' => $value), array('setting_key' => $key));
                return;
            }
            $wpdb->insert($table, array('setting_key' => $key, 'setting_value' => $value));
        }

        private function inline_css()
        {
            return ':root{--bg:#0f1217;--text:#f7f8fa;--gold:#d4af37;--bronze:#b58a4b;--glass:rgba(255,255,255,.08);--border:rgba(255,255,255,.2)}
            *{box-sizing:border-box}body.bd-page{margin:0;color:var(--text);font-family:Inter,Arial,sans-serif;background:radial-gradient(circle at top right,#273141,#0f1217 58%)}
            .bd-wrap{display:flex;min-height:100vh}.bd-sidebar{width:260px;position:fixed;top:0;bottom:0;left:0;padding:24px;background:#0e1116;border-right:1px solid rgba(212,175,55,.2)}
            .bd-sidebar h2{margin:0 0 16px;color:var(--gold)}.bd-nav{display:block;width:100%;padding:10px 12px;margin-bottom:10px;border:1px solid transparent;border-radius:10px;background:transparent;color:var(--text);text-align:left;cursor:pointer}
            .bd-nav.active,.bd-nav:hover{background:var(--glass);border-color:var(--border)}.bd-main{margin-left:260px;width:calc(100% - 260px);padding:24px}
            .bd-card{background:var(--glass);border:1px solid var(--border);backdrop-filter:blur(10px);border-radius:16px;padding:18px;box-shadow:0 14px 36px rgba(0,0,0,.28)}
            .bd-panel{display:none}.bd-panel.active{display:block}.bd-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
            input,textarea,select,button{width:100%;padding:11px 12px;margin:8px 0;border-radius:10px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.06);color:#fff}
            button{cursor:pointer;font-weight:700;background:linear-gradient(90deg,var(--gold),var(--bronze));color:#111}.bd-mini{width:auto;padding:7px 10px;margin-left:8px}
            .bd-table-item{display:flex;justify-content:space-between;gap:10px;padding:9px 4px;border-bottom:1px solid rgba(255,255,255,.1)}.bd-branding{text-align:center;margin-top:24px;font-family:Georgia,serif;color:rgba(255,255,255,.8)}
            .bd-store-wrap{max-width:1200px;margin:0 auto;padding:20px}.bd-store-header{text-align:center}.bd-store-header h1{font-size:42px;margin:0;color:var(--gold)}
            .bd-store-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin:16px 0}.bd-product img{width:100%;height:190px;object-fit:cover;border-radius:12px}
            .bd-chip{display:inline-block;padding:7px 12px;border:1px solid rgba(255,255,255,.2);border-radius:999px;margin:4px;cursor:pointer}.bd-chip.active{background:rgba(212,175,55,.25);border-color:var(--gold)}
            .bd-cart-card{position:sticky;bottom:10px}.bd-error{color:#ff8b8b;font-size:13px}.bd-modal{display:none;position:fixed;inset:0;z-index:9999}.bd-modal.open{display:block}
            .bd-modal-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.66)}.bd-modal-card{position:relative;max-width:720px;margin:6vh auto}.bd-modal-close{position:absolute;right:10px;top:10px;width:auto;padding:6px 10px;background:rgba(255,255,255,.15);color:#fff}
            .bd-modal-img{width:100%;height:320px;object-fit:cover;border-radius:12px}
            @media(max-width:860px){.bd-sidebar{position:relative;width:100%}.bd-main{margin-left:0;width:100%}.bd-wrap{display:block}.bd-grid-2{grid-template-columns:1fr}}';
        }

        private function inline_js()
        {
            return "(function($){
                const isAdmin = $('body').hasClass('bd-admin-page');
                const isStore = $('body').hasClass('bd-store-page');
                const money = (v)=> BakeDelightData.currency + ' ' + parseFloat(v || 0).toFixed(2);
                let state = {categories:[],products:[],orders:[],cart:[],filter:0,search:''};
                const esc = (s)=> $('<div>').text(s || '').html();

                function adminReq(payload, cb){
                    $.post(BakeDelightData.ajax_url, $.extend({action:'bd_admin_action', nonce:BakeDelightData.admin_nonce}, payload), cb);
                }
                function storeReq(payload, cb){
                    $.post(BakeDelightData.ajax_url, $.extend({action:'bd_store_action', nonce:BakeDelightData.store_nonce}, payload), cb);
                }

                function renderAdmin(){
                    $('#bd-product-category').html(state.categories.map(c=>`<option value='${c.id}'>${esc(c.name)}</option>`).join(''));
                    $('#bd-products-table').html('<div>' + (state.products.map(p=>`<div class=\"bd-table-item\"><div><strong>${esc(p.title)}</strong> - ${money(p.price)} (${esc(p.status)})</div><div><button class=\"bd-mini edit-product\" data-id=\"${p.id}\" type=\"button\">Edit</button><button class=\"bd-mini del-product\" data-id=\"${p.id}\" type=\"button\">Delete</button></div></div>`).join('') || 'No products yet') + '</div>');
                    $('#bd-categories-table').html('<div>' + (state.categories.map(c=>`<div class=\"bd-table-item\"><span>${esc(c.name)}</span><button class=\"bd-mini del-category\" data-id=\"${c.id}\" type=\"button\">Delete</button></div>`).join('') || 'No categories yet') + '</div>');
                    const q = (state.search || '').toLowerCase();
                    const logs = state.orders.filter(o => String(o.customer_name || '').toLowerCase().includes(q) || String(o.details || '').toLowerCase().includes(q));
                    $('#bd-orders-table').html('<div>' + (logs.map(o=>`<div class=\"bd-table-item\"><div><strong>${esc(o.customer_name)}</strong> | ${esc(o.delivery_time)} | ${money(o.total_price)}</div><div>${esc(o.status)}</div></div>`).join('') || 'No logs yet') + '</div>');
                }

                function loadAdmin(){
                    adminReq({sub_action:'list_all'}, function(res){
                        if(!res.success){ return; }
                        state.categories = res.data.categories || [];
                        state.products = res.data.products || [];
                        state.orders = res.data.orders || [];
                        renderAdmin();
                        if(res.data.settings){
                            $('#bd-settings-form [name=whatsapp_number]').val(res.data.settings.whatsapp_number || '');
                            $('#bd-settings-form [name=lead_time_hours]').val(res.data.settings.lead_time_hours || 24);
                            $('#bd-settings-form [name=currency]').val(res.data.settings.currency || 'PKR');
                        }
                    });
                }

                function renderStore(){
                    $('#bd-store-categories').html([`<span class='bd-chip ${state.filter===0 ? 'active' : ''}' data-id='0'>All</span>`].concat(state.categories.map(c=>`<span class='bd-chip ${state.filter==c.id ? 'active' : ''}' data-id='${c.id}'>${esc(c.name)}</span>`)).join(''));
                    $('#bd-store-grid').html(state.products.map(p=>`<article class='bd-card bd-product'><img src='${esc(p.image_url || 'https://images.unsplash.com/photo-1559620192-032c4bc4674e?auto=format&fit=crop&w=1200&q=80')}' alt='${esc(p.title)}'><h4>${esc(p.title)}</h4><p>${esc(p.description || '')}</p><p><strong>${money(p.price)}</strong></p><div class='bd-grid-2'><button class='quick-view' data-id='${p.id}' type='button'>Quick View</button><button class='add-cart' data-id='${p.id}' type='button'>Add to Cart</button></div></article>`).join('') || '<div class=\"bd-card\">No products available.</div>');
                    renderCart();
                }

                function renderCart(){
                    const rows = state.cart.map(c=>`<div class='bd-table-item'><span>${esc(c.title)} x ${c.qty}</span><span>${money(c.qty*c.price)}</span></div>`).join('');
                    const total = state.cart.reduce((sum,c)=> sum + (c.qty*c.price), 0);
                    $('#bd-cart-items').html(rows || 'Cart is empty.');
                    $('#bd-cart-total-value').text(money(total));
                }

                function loadStore(catId){
                    state.filter = parseInt(catId || 0, 10);
                    storeReq({sub_action:'list_products', cat_id:state.filter}, function(res){
                        if(!res.success){ return; }
                        state.products = res.data.products || [];
                        state.categories = res.data.categories || [];
                        renderStore();
                    });
                }

                $(document).on('click','.bd-nav',function(){
                    $('.bd-nav').removeClass('active'); $(this).addClass('active');
                    $('.bd-panel').removeClass('active');
                    $('#bd-panel-' + $(this).data('panel')).addClass('active');
                });

                if (isAdmin){
                    loadAdmin();
                    $('#bd-order-search').on('input', function(){ state.search = $(this).val() || ''; renderAdmin(); });
                    $('#bd-category-form').on('submit', function(e){
                        e.preventDefault();
                        adminReq({sub_action:'save_category', category_id:$(this).find('[name=category_id]').val(), name:$(this).find('[name=name]').val()}, function(){ loadAdmin(); $('#bd-category-form')[0].reset(); });
                    });
                    $(document).on('click','.del-category',function(){ adminReq({sub_action:'delete_category', id:$(this).data('id')}, loadAdmin); });
                    $('#bd-product-form').on('submit', function(e){
                        e.preventDefault();
                        const payload = {sub_action:'save_product'};
                        $(this).serializeArray().forEach(x => payload[x.name] = x.value);
                        adminReq(payload, function(){ loadAdmin(); $('#bd-product-form')[0].reset(); $('#bd-product-form [name=product_id]').val(''); });
                    });
                    $(document).on('click','.del-product',function(){ adminReq({sub_action:'delete_product', id:$(this).data('id')}, loadAdmin); });
                    $(document).on('click','.edit-product',function(){
                        const p = state.products.find(x => parseInt(x.id,10) === parseInt($(this).data('id'),10));
                        if(!p){ return; }
                        const f = $('#bd-product-form');
                        f.find('[name=product_id]').val(p.id); f.find('[name=title]').val(p.title); f.find('[name=description]').val(p.description);
                        f.find('[name=price]').val(p.price); f.find('[name=weight_options]').val(p.weight_options); f.find('[name=image_url]').val(p.image_url);
                        f.find('[name=cat_id]').val(p.cat_id); f.find('[name=status]').val(p.status); f.find('[name=stock_status]').val(p.stock_status);
                        window.scrollTo({top:0, behavior:'smooth'});
                    });
                    $('#bd-settings-form').on('submit', function(e){
                        e.preventDefault();
                        const payload = {sub_action:'save_settings'};
                        $(this).serializeArray().forEach(x => payload[x.name] = x.value);
                        adminReq(payload, function(){ alert('Settings updated'); loadAdmin(); });
                    });
                }

                if (isStore){
                    loadStore(0);
                    flatpickr('#bd-delivery-time', {enableTime:true, dateFormat:'Y-m-d H:i', minDate:new Date(Date.now() + (24 * 60 * 60 * 1000))});
                    $(document).on('click','.bd-chip',function(){ loadStore($(this).data('id')); });
                    $(document).on('click','.add-cart',function(){
                        const p = state.products.find(x => parseInt(x.id,10) === parseInt($(this).data('id'),10));
                        if(!p){ return; }
                        const item = state.cart.find(x => x.id == p.id);
                        if(item){ item.qty += 1; } else { state.cart.push({id:p.id,title:p.title,price:parseFloat(p.price),qty:1,image_url:p.image_url}); }
                        renderCart();
                    });
                    $(document).on('click','.quick-view',function(){
                        const p = state.products.find(x => parseInt(x.id,10) === parseInt($(this).data('id'),10));
                        if(!p){ return; }
                        $('#bd-modal-content').html(`<img class='bd-modal-img' src='${esc(p.image_url || 'https://images.unsplash.com/photo-1559620192-032c4bc4674e?auto=format&fit=crop&w=1200&q=80')}' alt='${esc(p.title)}'><h3>${esc(p.title)}</h3><p>${esc(p.description || '')}</p><p><strong>${money(p.price)}</strong></p><button class='add-cart' data-id='${p.id}' type='button'>Add to Cart</button>`);
                        $('#bd-quick-view-modal').addClass('open');
                    });
                    $('#bd-modal-close, .bd-modal-backdrop').on('click', function(){ $('#bd-quick-view-modal').removeClass('open'); });

                    $('#bd-checkout-form').on('submit', function(e){
                        e.preventDefault();
                        $('#bd-checkout-error').text('');
                        if(state.cart.length === 0){ $('#bd-checkout-error').text('Your cart is empty.'); return; }
                        const name = $(this).find('[name=customer_name]').val();
                        const delivery = $(this).find('[name=delivery_time]').val();
                        const selectedTs = new Date(delivery.replace(' ', 'T')).getTime();
                        const minTs = Date.now() + (parseInt(BakeDelightData.lead_time_hours, 10) * 60 * 60 * 1000);
                        if(!name || name.length < 2){ $('#bd-checkout-error').text('Please enter your full name.'); return; }
                        if(!delivery || !selectedTs || selectedTs < minTs){ $('#bd-checkout-error').text('Order must be placed 24 hours in advance.'); return; }

                        const items = state.cart.map(c => `${c.title} x ${c.qty}`).join(', ');
                        const total = state.cart.reduce((sum,c)=> sum + (c.qty*c.price), 0);
                        const imgRef = state.cart[0] && state.cart[0].image_url ? state.cart[0].image_url : 'https://en.wikipedia.org/wiki/Main_product';
                        const message = `*Bake Delight Order*\\n------------------\\nCustomer: ${name}\\nItems: ${items}\\nTotal: ${money(total)}\\nDelivery: ${delivery}\\nImage Reference: ${imgRef}`;

                        storeReq({
                            sub_action:'log_order',
                            customer_name:name,
                            details:message,
                            total_price:total,
                            delivery_time:delivery
                        }, function(res){
                            if(!res.success){ $('#bd-checkout-error').text((res.data && res.data.message) ? res.data.message : 'Unable to place order.'); return; }
                            const wa = (res.data.whatsapp || BakeDelightData.whatsapp || '').replace(/[^0-9]/g, '');
                            if(!wa){ $('#bd-checkout-error').text('Please contact admin: WhatsApp not configured.'); return; }
                            window.open('https://wa.me/' + wa + '?text=' + encodeURIComponent(message), '_blank');
                            state.cart = []; renderCart(); $('#bd-checkout-form')[0].reset();
                        });
                    });
                }
            })(jQuery);";
        }
    }

    new BakeDelightPlugin();
}

