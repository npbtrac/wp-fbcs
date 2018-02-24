<?php
/**
 * Created by PhpStorm.
 * User: tracnguyen
 * Date: 2/15/18
 * Time: 11:23 PM
 */

use \Enpii\WpPlugin\Fbcs\Fbcs;

?>
<link href="<?php echo Fbcs::plugin_dir_url() . '/assets/dist/css/admin.css'; ?>" rel="stylesheet"
      type="text/css">
<div class="options">
    <div class="options_header">
        <h1><?= __( 'Facebook Comments Sync', Fbcs::text_domain() ) ?></h1>
    </div>

    <div class="options">
        <div class="options_left">
            <div class="inside">
                <form method="post" action="options.php" id="options">
					<?php settings_fields( Fbcs::OPTION_GROUP_NAME ); ?>
                    <h3 class="title"><?= __( 'Facebook App Settings', Fbcs::text_domain() ) ?></h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">
                                <label for="fb_app_id"><?= __( 'Facebook App ID', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <input id="fb_app_id" class="standard-input" type="text" name="fbcs[fb_app_id]"
                                       value="<?php echo $options['fb_app_id']; ?>"/>
                                <strong>
                                    <a href="https://developers.facebook.com/apps"
                                       style="text-decoration:none; padding-left: 10px;"
                                       target="_blank">
										<?= __( 'How to set up your Facebook App ID', Fbcs::text_domain() ) ?>
                                    </a>
                                </strong>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="fb_app_secret"><?= __( 'Facebook App Secret', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <input id="fb_app_secret" class="standard-input" type="text" name="fbcs[fb_app_secret]"
                                       value="<?php echo $options['fb_app_secret']; ?>"/>
                                <strong>
                                    <a href="https://developers.facebook.com/apps"
                                       style="text-decoration:none; padding-left: 10px;" target="_blank">
										<?= __( 'How to set up your Facebook App Secret', Fbcs::text_domain() ) ?>
                                    </a>
                                </strong>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="fb_moderators"><?= __( 'Facebook Moderators', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <input id="fb_moderators" type="text" name="fbcs[fb_moderators]"
                                       value="<?php echo $options['fb_moderators']; ?>" size="20"/>
                                <strong>
                                    <a href="https://developers.facebook.com/tools/comments<?= $options['fb_app_id'] ? "?id=" . $options['fb_app_id'] . "&view=queue" : '' ?>"
                                       style="text-decoration:none; padding-left:10px;" target="_blank">
										<?= __( 'Comment Moderation', Fbcs::text_domain() ) ?>
                                    </a>
                                </strong>
                                <br/>
                                <small>
									<?= __( 'By default, all admins to the App ID can moderate comments. To add moderators, enter each Facebook Profile ID by a comma <strong>without spaces</strong>. To find your Facebook User ID, click <a href="https://developers.facebook.com/tools/explorer/?method=GET&path=me" target="blank">here</a> where you will see your own. To view someone else\'s, replace "me" with their username in the input provided.', Fbcs::text_domain() ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>

                    <h3 class="title"><?= __( 'Main Settings', Fbcs::text_domain() ) ?></h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label for="fbjs">FBML</label></th>
                            <td>
                                <input id="fbjs" name="fbcs[fbjs]" type="checkbox"
                                       value="1" <?php checked( 1, $options['fbjs'] ); ?> />
                                <small><?= __( 'If you already have XFBML enabled somewhere else, disable this.', Fbcs::text_domain() ) ?></small>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="insert_after_content">Insert Affer Content</label></th>
                            <td>
                                <input id="fbjs" name="fbcs[insert_after_content]" type="checkbox"
                                       value="1" <?php checked( 1, $options['insert_after_content'] ); ?> />
                                <small><?= __( 'Check this if you want to automatically insert FB comments plugin after content of a post in single.', Fbcs::text_domain() ) ?></small>
                            </td>
                        </tr>
                    </table>

                    <h3 class="title"><?= __( 'Display Settings', Fbcs::text_domain() ) ?></h3>
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row"><label for="language"><?= __( 'Language', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <select id="language" name="fbcs[language]">
                                    <option value="af_ZA" <?php selected( $options['language'], 'af_ZA' ); ?>>
                                        Afrikaans
                                    </option>
                                    <option value="ar_AR" <?php selected( $options['language'], 'ar_AR' ); ?>>
                                        Arabic
                                    </option>
                                    <option value="az_AZ" <?php selected( $options['language'], 'az_AZ' ); ?>>
                                        Azerbaijani
                                    </option>
                                    <option value="be_BY" <?php selected( $options['language'], 'be_BY' ); ?>>
                                        Belarusian
                                    </option>
                                    <option value="bg_BG" <?php selected( $options['language'], 'bg_BG' ); ?>>
                                        Bulgarian
                                    </option>
                                    <option value="bn_IN" <?php selected( $options['language'], 'bn_IN' ); ?>>
                                        Bengali
                                    </option>
                                    <option value="bs_BA" <?php selected( $options['language'], 'bs_BA' ); ?>>
                                        Bosnian
                                    </option>
                                    <option value="ca_ES" <?php selected( $options['language'], 'ca_ES' ); ?>>
                                        Catalan
                                    </option>
                                    <option value="cs_CZ" <?php selected( $options['language'], 'cs_CZ' ); ?>>
                                        Czech
                                    </option>
                                    <option value="cy_GB" <?php selected( $options['language'], 'cy_GB' ); ?>>
                                        Welsh
                                    </option>
                                    <option value="da_DK" <?php selected( $options['language'], 'da_DK' ); ?>>
                                        Danish
                                    </option>
                                    <option value="de_DE" <?php selected( $options['language'], 'de_DE' ); ?>>
                                        German
                                    </option>
                                    <option value="el_GR" <?php selected( $options['language'], 'el_GR' ); ?>>
                                        Greek
                                    </option>
                                    <option value="en_GB" <?php selected( $options['language'], 'en_GB' ); ?>>
                                        English
                                        (UK)
                                    </option>
                                    <option value="en_PI" <?php selected( $options['language'], 'en_PI' ); ?>>
                                        English
                                        (Pirate)
                                    </option>
                                    <option value="en_UD" <?php selected( $options['language'], 'en_UD' ); ?>>
                                        English
                                        (Upside Down)
                                    </option>
                                    <option value="en_US" <?php selected( $options['language'], 'en_US' ); ?>>
                                        English
                                        (US)
                                    </option>
                                    <option value="eo_EO" <?php selected( $options['language'], 'eo_EO' ); ?>>
                                        Esperanto
                                    </option>
                                    <option value="es_ES" <?php selected( $options['language'], 'es_ES' ); ?>>
                                        Spanish
                                        (Spain)
                                    </option>
                                    <option value="es_LA" <?php selected( $options['language'], 'es_LA' ); ?>>
                                        Spanish
                                    </option>
                                    <option value="et_EE" <?php selected( $options['language'], 'et_EE' ); ?>>
                                        Estonian
                                    </option>
                                    <option value="eu_ES" <?php selected( $options['language'], 'eu_ES' ); ?>>
                                        Basque
                                    </option>
                                    <option value="fa_IR" <?php selected( $options['language'], 'fa_IR' ); ?>>
                                        Persian
                                    </option>
                                    <option value="fb_LT" <?php selected( $options['language'], 'fb_LT' ); ?>>
                                        Leet
                                        Speak
                                    </option>
                                    <option value="fi_FI" <?php selected( $options['language'], 'fi_FI' ); ?>>
                                        Finnish
                                    </option>
                                    <option value="fo_FO" <?php selected( $options['language'], 'fo_FO' ); ?>>
                                        Faroese
                                    </option>
                                    <option value="fr_CA" <?php selected( $options['language'], 'fr_CA' ); ?>>
                                        French
                                        (Canada)
                                    </option>
                                    <option value="fr_FR" <?php selected( $options['language'], 'fr_FR' ); ?>>
                                        French
                                        (France)
                                    </option>
                                    <option value="fy_NL" <?php selected( $options['language'], 'fy_NL' ); ?>>
                                        Frisian
                                    </option>
                                    <option value="ga_IE" <?php selected( $options['language'], 'ga_IE' ); ?>>
                                        Irish
                                    </option>
                                    <option value="gl_ES" <?php selected( $options['language'], 'gl_ES' ); ?>>
                                        Galician
                                    </option>
                                    <option value="he_IL" <?php selected( $options['language'], 'he_IL' ); ?>>
                                        Hebrew
                                    </option>
                                    <option value="hi_IN" <?php selected( $options['language'], 'hi_IN' ); ?>>
                                        Hindi
                                    </option>
                                    <option value="hr_HR" <?php selected( $options['language'], 'hr_HR' ); ?>>
                                        Croatian
                                    </option>
                                    <option value="hu_HU" <?php selected( $options['language'], 'hu_HU' ); ?>>
                                        Hungarian
                                    </option>
                                    <option value="hy_AM" <?php selected( $options['language'], 'hy_AM' ); ?>>
                                        Armenian
                                    </option>
                                    <option value="id_ID" <?php selected( $options['language'], 'id_ID' ); ?>>
                                        Indonesian
                                    </option>
                                    <option value="is_IS" <?php selected( $options['language'], 'is_IS' ); ?>>
                                        Icelandic
                                    </option>
                                    <option value="it_IT" <?php selected( $options['language'], 'it_IT' ); ?>>
                                        Italian
                                    </option>
                                    <option value="ja_JP" <?php selected( $options['language'], 'ja_JP' ); ?>>
                                        Japanese
                                    </option>
                                    <option value="ka_GE" <?php selected( $options['language'], 'ka_GE' ); ?>>
                                        Georgian
                                    </option>
                                    <option value="km_KH" <?php selected( $options['language'], 'km_KH' ); ?>>
                                        Khmer
                                    </option>
                                    <option value="ko_KR" <?php selected( $options['language'], 'ko_KR' ); ?>>
                                        Korean
                                    </option>
                                    <option value="ku_TR" <?php selected( $options['language'], 'ku_TR' ); ?>>
                                        Kurdish
                                    </option>
                                    <option value="la_VA" <?php selected( $options['language'], 'la_VA' ); ?>>
                                        Latin
                                    </option>
                                    <option value="lt_LT" <?php selected( $options['language'], 'lt_LT' ); ?>>
                                        Lithuanian
                                    </option>
                                    <option value="lv_LV" <?php selected( $options['language'], 'lv_LV' ); ?>>
                                        Latvian
                                    </option>
                                    <option value="mk_MK" <?php selected( $options['language'], 'mk_MK' ); ?>>
                                        Macedonian
                                    </option>
                                    <option value="ml_IN" <?php selected( $options['language'], 'ml_IN' ); ?>>
                                        Malayalam
                                    </option>
                                    <option value="ms_MY" <?php selected( $options['language'], 'ms_MY' ); ?>>
                                        Malay
                                    </option>
                                    <option value="nb_NO" <?php selected( $options['language'], 'nb_NO' ); ?>>
                                        Norwegian (bokmal)
                                    </option>
                                    <option value="ne_NP" <?php selected( $options['language'], 'ne_NP' ); ?>>
                                        Nepali
                                    </option>
                                    <option value="nl_NL" <?php selected( $options['language'], 'nl_NL' ); ?>>
                                        Dutch
                                    </option>
                                    <option value="nn_NO" <?php selected( $options['language'], 'nn_NO' ); ?>>
                                        Norwegian (nynorsk)
                                    </option>
                                    <option value="pa_IN" <?php selected( $options['language'], 'pa_IN' ); ?>>
                                        Punjabi
                                    </option>
                                    <option value="pl_PL" <?php selected( $options['language'], 'pl_PL' ); ?>>
                                        Polish
                                    </option>
                                    <option value="ps_AF" <?php selected( $options['language'], 'ps_AF' ); ?>>
                                        Pashto
                                    </option>
                                    <option value="pt_BR" <?php selected( $options['language'], 'pt_BR' ); ?>>
                                        Portuguese (Brazil)
                                    </option>
                                    <option value="pt_PT" <?php selected( $options['language'], 'pt_PT' ); ?>>
                                        Portuguese (Portugal)
                                    </option>
                                    <option value="ro_RO" <?php selected( $options['language'], 'ro_RO' ); ?>>
                                        Romanian
                                    </option>
                                    <option value="ru_RU" <?php selected( $options['language'], 'ru_RU' ); ?>>
                                        Russian
                                    </option>
                                    <option value="sk_SK" <?php selected( $options['language'], 'sk_SK' ); ?>>
                                        Slovak
                                    </option>
                                    <option value="sl_SI" <?php selected( $options['language'], 'sl_SI' ); ?>>
                                        Slovenian
                                    </option>
                                    <option value="sq_AL" <?php selected( $options['language'], 'sq_AL' ); ?>>
                                        Albanian
                                    </option>
                                    <option value="sr_RS" <?php selected( $options['language'], 'sr_RS' ); ?>>
                                        Serbian
                                    </option>
                                    <option value="sv_SE" <?php selected( $options['language'], 'sv_SE' ); ?>>
                                        Swedish
                                    </option>
                                    <option value="sw_KE" <?php selected( $options['language'], 'sw_KE' ); ?>>
                                        Swahili
                                    </option>
                                    <option value="ta_IN" <?php selected( $options['language'], 'ta_IN' ); ?>>
                                        Tamil
                                    </option>
                                    <option value="te_IN" <?php selected( $options['language'], 'te_IN' ); ?>>
                                        Telugu
                                    </option>
                                    <option value="th_TH" <?php selected( $options['language'], 'th_TH' ); ?>>
                                        Thai
                                    </option>
                                    <option value="tl_PH" <?php selected( $options['language'], 'tl_PH' ); ?>>
                                        Filipino
                                    </option>
                                    <option value="tr_TR" <?php selected( $options['language'], 'tr_TR' ); ?>>
                                        Turkish
                                    </option>
                                    <option value="uk_UA" <?php selected( $options['language'], 'uk_UA' ); ?>>
                                        Ukrainian
                                    </option>
                                    <option value="vi_VN" <?php selected( $options['language'], 'vi_VN' ); ?>>
                                        Vietnamese
                                    </option>
                                    <option value="zh_CN" <?php selected( $options['language'], 'zh_CN' ); ?>>
                                        Simplified Chinese (China)
                                    </option>
                                    <option value="zh_HK" <?php selected( $options['language'], 'zh_HK' ); ?>>
                                        Traditional Chinese (Hong Kong)
                                    </option>
                                    <option value="zh_TW" <?php selected( $options['language'], 'zh_TW' ); ?>>
                                        Traditional Chinese (Taiwan)
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label
                                        for="scheme"><?= __( 'Colour Scheme', Fbcs::text_domain() ) ?></label></th>
                            <td>
                                <select id="scheme" name="fbcs[scheme]">
                                    <option value="light"<?php if ( $options['scheme'] == 'light' ) {
										echo ' selected="selected"';
									} ?>>
										<?= __( 'Light', Fbcs::text_domain() ) ?>
                                    </option>
                                    <option value="dark"<?php if ( $options['scheme'] == 'dark' ) {
										echo ' selected="selected"';
									} ?>>
										<?= __( 'Dark', Fbcs::text_domain() ) ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label
                                        for="num"><?= __( 'Number of Comments', Fbcs::text_domain() ) ?></label></th>
                            <td>
                                <input id="num" type="text" name="fbcs[num]" value="<?php echo $options['num']; ?>"/>
                                <small><?= __( 'Default number for comments to be shown up is <strong>12</strong>', Fbcs::text_domain() ) ?></small>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label
                                        for="order"><?= __( 'Comments Order', Fbcs::text_domain() ) ?></label></th>
                            <td>
                                <select id="order" name="fbcs[order]">
                                    <option value="reverse_time"<?php if ( $options['order'] == 'reverse_time' ) {
										echo ' selected="selected"';
									} ?>>
										<?= __( 'Newest Comments First', Fbcs::text_domain() ) ?>
                                    </option>
                                    <option value="time"<?php if ( $options['order'] == 'time' ) {
										echo ' selected="selected"';
									} ?>>
										<?= __( 'Oldest Comments First', Fbcs::text_domain() ) ?>
                                    </option>
                                    <option value="social"<?php if ( $options['order'] == 'social' ) {
										echo ' selected="selected"';
									} ?>>
										<?= __( 'Highest Quality First', Fbcs::text_domain() ) ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                                <label for="width"><?= __( 'Width', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <input id="width" type="text" name="fbcs[width]"
                                       value="<?php echo $options['width']; ?>"/>
                                <small>
									<?= __( 'Default is <strong>100%</strong>. Keep at this to ensure the comment box is responsive', Fbcs::text_domain() ) ?>
                                </small>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="title_text"><?= __( 'Title', Fbcs::text_domain() ) ?></label>
                            </th>
                            <td>
                                <input id="title_text" type="text" name="fbcs[title_text]"
                                       value="<?php echo $options['title_text']; ?>"/>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>"/>
                    </p>
                </form>


                <h3 class="title"><?= __( 'Facebook Sync', Fbcs::text_domain() ) ?></h3>


                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <a id="sync-latest-posts" href="javascript:" class="button-primary">
								<?= __( 'Sync for all content', Fbcs::text_domain() ) ?>
                            </a>
                        </th>
                        <td id="synctext">
                            <small><?= __( 'Sync FB comments to WP comment for all kind of post types, follow order from latest data to oldest data', Fbcs::text_domain() ) ?></small>
                        </td>
                    </tr>
                </table>

                <script>
                    jQuery(document).ready(function ($) {
                        var $syncLatestPosts = $('#sync-latest-posts');
                        var syncLatestPostsText = $syncLatestPosts.text();

                        var posts_per_page = 2;

                        function syncLatestPosts(posts_per_page, offset) {
                            $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: ajaxurl,
                                data: {
                                    'action': 'fbcs_sync_latest_posts',
                                    'posts_per_page': posts_per_page,
                                    'offset': offset,
                                    'limit': -1
                                },
                                complete: function (event) {
                                },
                                success: function (response) {
                                    var found_posts = parseInt(response.found_posts);
                                    var offset = parseInt(response.offset);

                                    if (found_posts > (offset + 1) * posts_per_page) {
                                        $syncLatestPosts.text('Syncing...' + (posts_per_page * (offset + 1)) + ' to ' + (posts_per_page * (offset + 2) > found_posts ? found_posts : posts_per_page * (offset + 2)) + ' of ' + found_posts);
                                        syncLatestPosts(posts_per_page, offset + 1);
                                    } else {
                                        $syncLatestPosts.text(syncLatestPostsText).removeAttr('disabled');
                                        console.log('Sync completed!');
                                    }
                                },
                                error: function (exception) {
                                    console.log('Ajax failed!');
                                    console.log(exception);
                                }
                            });
                        }

                        $syncLatestPosts.click(function () {
                            $(this).text('Syncing...').attr('disabled', 'disabled');
                            syncLatestPosts(posts_per_page, 0);
                        });
                    }(jQuery));
                </script>
            </div>
        </div>
        <div class="options_right">
            <div class="donation">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="CYMH524WGLM8Q">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>


                <p>If you like this plugin, buy me a coffee.</p>
            </div>

        </div>
    </div>
</div>