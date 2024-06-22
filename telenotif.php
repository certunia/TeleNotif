<?php
/*
 * Plugin name: Telenotif
 * Plugin URI:  https://github.com/certunia/Telenotif
 * Description: Simple bot notification about new order in woocommerce shop
 * Version: 1.0.0
 * Author: Daniil Tiuneev
 * Author URI: https://github.com/certunia
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: telenotif
 * Domain Path: /languages
 *
 * Network: true
 */

function wpdocs_register_menu_page() {
    add_menu_page('TeleNotif', 'TeleNotif', 'manage_options', 'telenotif/admin.php', 'telenotif/admin.php', 'dashicons-info');
    // Add a new top-level menu (ill-advised):

    add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'telenotif/admin.php');

    //add_submenu_page('telenotif', 'Page title', 'Sub-menu title', 'manage_options', 'my-submenu-handle', 'my_magic_function');
}

add_action( 'admin_menu', 'wpdocs_register_menu_page' );



// mt_settings_page() displays the page content for the Test Settings submenu
function mt_settings_page() {

    //must check that the user has the required capability
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names
    $opt_name = 'mt_favorite_color';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'mt_favorite_color';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put a "settings saved" message on the screen

?>
    <div class="updated">
        <p>
            <strong>
                <?php _e('settings saved.', 'menu-test' ); ?>
            </strong>
        </p>
    </div>
<?php
    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2 class='wp-heading-inline'>" . __( 'Menu Test Plugin Settings', 'menu-test' ) . "</h2>";
    // settings form

    ?>

    <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <p><?php _e("Favorite Color:", 'menu-test' ); ?>
            <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
        </p><hr />

        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>

    </form>
</div>

<?php

}

add_action('woocommerce_checkout_order_processed', 'manual_sending', 10, 1);

//manual_sending();
function manual_sending($order_id)
{
    $msg = "";

    $order = wc_get_order( $order_id );

    if ($order) {
        //время
        $msg = $msg . "Когда: " . $order->get_date_created()->format('d-m-Y H:i');
        $msg = $msg . "\r\n";
        //имя
        $msg = $msg . "Имя: " . $order->get_billing_first_name();
        $msg = $msg . "\r\n";
        //телефон
        $msg = $msg . "Телефон: " . $order->get_billing_phone();
        $msg = $msg . "\r\n";
        //населенный пункт
        $msg = $msg . "Город: " . $order->get_billing_city();
        $msg = $msg . "\r\n";
        //адрес
        $msg = $msg . "Адрес: " . $order->get_billing_address_1();
        $msg = $msg . "\r\n";
        $msg = $msg . "Адрес2: " . $order->get_billing_address_2();
        $msg = $msg . "\r\n";
        //детали
        $msg = $msg . "Дополнительные детали: " . $order->get_customer_note();
        $msg = $msg . "\r\n";
        $msg = $msg . "\r\n";
        //cам товар


        $msg = $msg . "Все заказы: https://profmagdom.ru/wp-admin/edit.php?post_type=shop_order";

        //foreach ($order->get_items() as $item_id => $item) {
        //    $msg = $msg . $name = $item->get_name();
        //    $msg = $msg . "\r\n";
        //    $quantity = $item->get_quantity();
        //    $total = $item->get_total();
        //    $per_one = $item->get_product()->get_price();
        //    $msg = $msg . $quantity . " * " . $per_one . " = " . $total;
        //    $msg = $msg . "\r\n";
        //}

//        $msg = $msg . "Order: " . $order;
//        $msg = $msg . "\r\n";

        //тотал
        $msg = $msg . "\r\n";
        $msg = $msg . "Всего: " . $order->get_total();
    }

//    //example https://api.telegram.org/bot5170247503:AAFnSsE5zO8gb5LgJdsO3d8HMPbk-26MsD0/sendMessage?chat_id=-1001777542224&text=notif
//    // Telegram function which you can call
    function sendMessage($chatID, $messaggio, $token)
    {
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatID;
        $url = $url . "&text=" . urlencode($messaggio);
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    // Set your Bot ID and Chat ID.
    $telegrambot = '5170247503:AAFnSsE5zO8gb5LgJdsO3d8HMPbk-26MsD0';
    $telegramchatid = "-1001777542224";

    // Function call with your own text or variable
    sendMessage($telegramchatid, $msg, $telegrambot);
}
