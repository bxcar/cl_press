<?php
/**
 * @package Sydney
 */
error_reporting(E_ERROR);
?>
<?php
if (!$_GET['sort_id'])
    if ($_GET['select_id']) {
        require "../../../wp-config.php";
        $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (!$db) {
            exit('Error' . mysqli_error($db));
        }
        mysqli_query($db, "SET NAMES utf8mb4");

        function get_select_option($db, $id = false)
        {
            $sql_select = "SELECT p.post_title FROM wp_posts p WHERE p.post_status = 'publish' AND p.post_type = 'post'";
            $select = array();
            $select_total = array();
            $result = mysqli_query($db, $sql_select) or die(mysqli_error($db));
            $select_total[0] = "Все страны";
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $select[$i] = mysqli_fetch_assoc($result);
//            $select[$i]['post_title'] = $sql_select[$i]['post_title'];
                /*$goods[$i]['discount'] = $goods_discount[$i]['meta_value'];
                $goods[$i]['post_id'] = $goods_discount[$i]['post_id'];*/
                $select_total[$i + 1] = $select[$i]['post_title'];
            }
            return $select_total;
        }

        $select_total = get_select_option($db, $id);
        foreach ($select_total as $item) {
            ?>
            <script>
                var select = document.getElementById("country-select");
                var node = document.createElement("option");
                node.setAttribute("value", "<?= $item ?>");
                var textnode = document.createTextNode("<?= $item ?>");
                node.appendChild(textnode);
                select.appendChild(node);
            </script>
            <?php
        }
    }

if (!$_GET['select_id'])
    if ($_GET['sort_id']) {
        require "../../../wp-config.php";
        $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (!$db) {
            exit('Error' . mysqli_error($db));
        }
        mysqli_query($db, "SET NAMES utf8mb4");

        function get_goods($db, $id = false)
        {
            $sql = "SELECT p.id, p.post_title, p.post_content, t.name FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r
WHERE t.term_id=tx.term_id 
AND tx.taxonomy='post_tag' 
AND tx.term_taxonomy_id=r.term_taxonomy_id 
AND r.object_id=p.id
AND p.post_status != 'trash'";

            $sql1 = "SELECT p.guid FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r, wp_postmeta pmet
WHERE p.post_status != 'trash'
AND p.post_type = 'attachment'
AND t.term_id=tx.term_id
AND tx.taxonomy='post_tag'
AND tx.term_taxonomy_id=r.term_taxonomy_id
AND r.object_id=p.post_parent
AND pmet.meta_value = p.id";

            $sql2 = "SELECT pmet.meta_value, pmet.post_id FROM wp_posts p, wp_postmeta pmet, wp_terms t
WHERE p.post_status != 'trash'
AND pmet.meta_key = 'DC'
AND pmet.post_id = p.id";

            $sql_for_counry_title_and_other = "SELECT p.id, p.post_title, p.post_content, t.name FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r
WHERE t.term_id=tx.term_id 
AND tx.taxonomy='post_tag' 
AND tx.term_taxonomy_id=r.term_taxonomy_id 
AND r.object_id=p.id
AND p.post_status != 'trash'";
            $sql_for_country_image = "SELECT p.guid FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r, wp_postmeta pmet
WHERE p.post_status != 'trash'
AND p.post_type = 'attachment'
AND t.term_id=tx.term_id
AND tx.taxonomy='post_tag'
AND tx.term_taxonomy_id=r.term_taxonomy_id
AND r.object_id=p.post_parent
AND pmet.meta_value = p.id";

            if ($id) {
                if ($id == 'price_sorta') {
                    $sql .= " ORDER BY length(t.name), t.name ASC";
                    $sql2 .= " ORDER BY length(t.name), t.name ASC";
                } else if ($id == 'price_sortb') {
                    $sql .= " ORDER BY cast(t.name as unsigned) DESC";
                    $sql2 .= " ORDER BY cast(t.name as unsigned) DESC";
                } else if ($id == 'Все страны' or $id == 'price-default') {
//                $sql .= " ORDER BY cast(p.post_date as unsigned) DESC";
                    $sql = "SELECT p.id, p.post_title, p.post_content, t.name FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r
                        WHERE t.term_id=tx.term_id 
                        AND tx.taxonomy='post_tag' 
                        AND tx.term_taxonomy_id=r.term_taxonomy_id 
                        AND r.object_id=p.id
                        AND p.post_status != 'trash'
                        ORDER BY cast(p.id AS UNSIGNED) DESC";
//                AND (SELECT p.id FROM wp_posts po WHERE po.id = p.post_parent)
                } else {
                    $sql = $sql_for_counry_title_and_other . " AND p.post_title = '$id'";
                }
            }
            if ($id) {
                if ($id == 'price_sorta') {
                    $sql1 .= " ORDER BY length(t.name), t.name ASC";
                } else if ($id == 'price_sortb') {
                    $sql1 .= " ORDER BY cast(t.name as unsigned) DESC";
                } else if ($id == 'Все страны' or $id == 'price-default') {
                    $sql1 = "SELECT p.guid FROM wp_posts p, wp_terms t, wp_term_taxonomy tx, wp_term_relationships r, wp_postmeta pmet
                            WHERE p.post_status != 'trash'
                            AND p.post_type = 'attachment'
                            AND t.term_id=tx.term_id
                            AND tx.taxonomy='post_tag'
                            AND tx.term_taxonomy_id=r.term_taxonomy_id
                            AND r.object_id=p.post_parent
                            AND pmet.meta_value = p.id
                            AND (SELECT p.id FROM wp_posts po WHERE po.id = p.post_parent)
                            ORDER BY cast(p.post_parent AS UNSIGNED) DESC";
                    /*t.term_id=tx.term_id
                    AND*/
                    /*AND tx.taxonomy='post_tag'
                    AND tx.term_taxonomy_id=r.term_taxonomy_id
                    AND r.object_id=p.id*/
//                AND (SELECT p.guid FROM wp_posts po WHERE po.post_title = '$id' AND po.id = p.post_parent)
//                $sql1 .= " ORDER BY p.post_date DESC";
                } else {
                    $sql1 = $sql_for_country_image . " AND (SELECT p.id FROM wp_posts po WHERE po.post_title = '$id' AND po.id = p.post_parent)";
                }
            }
            $unic = array();
            $goods = array();
            $goods_img = array();
            $goods_discount = array();
            $result = mysqli_query($db, $sql) or die(mysqli_error($db));
            $result1 = mysqli_query($db, $sql1) or die(mysqli_error($db));
            $result2 = mysqli_query($db, $sql2) or die(mysqli_error($db));
            $count_for_unic = 0;
            for ($i = 0; $i < mysqli_num_rows($result); $i++) {
                $goods[$i] = mysqli_fetch_assoc($result);
                $goods_img[$i] = mysqli_fetch_assoc($result1);
                $goods_discount[$i] = mysqli_fetch_assoc($result2);
                $goods[$i]['guid'] = $goods_img[$i]['guid'];
                $goods[$i]['discount'] = $goods_discount[$i]['meta_value'];
                $goods[$i]['post_id'] = $goods_discount[$i]['post_id'];
//                $unic[$i] = $goods[$i]['post_title'];

                if($id == 'Все страны' or $id == 'price-default') {
                    $count = 0;
                    foreach ($goods as $value) {
                        if ($value['post_title'] == $goods[$i]['post_title']) {
                            $count++;
                            $unic[$count_for_unic] = $goods[$i];
                            $count_for_unic++;
                        }
                    }
//                    print_r ($unic);
                    /*$length_unic = count($unic);
                    foreach ($unic as $value) {
                        for ($ix = 0; $ix < $length_unic; $ix++) {
                            if ($value['post_title'] == $goods[$i]['post_title']) {
                                unset($unic[$ix]);
                            }
                        }
                    }*/

                    if ($count > 1) {
                        if (isset($goods[$i])) {
                            unset($goods[$i]);
                        }
                    }
                }
            }
//            print_r ($unic);
            
            /*printf("\nRepeated elements:\n");
            for($iq=0,$tmp=0; $iq<count($unic)-1; $iq++){
                if ($unic[$iq]['post_title']==$unic[$iq+1]['post_title']) {
                    print_r($unic[$iq]);
                    for($jq=($iq+1); $jq<count($unic) && $unic[$jq]['post_title']==$unic[$iq]['post_title']; $jq++){
                        $tmp++;
                        print_r($unic[$jq]);
                    }
                    $iq=$jq-1;
                }
            }*/

//            $unic_chek = $unic;
           /* foreach ($unic as $item) {
                foreach ($unic_chek as $item_check) {
                    if($item != $item_check)
                }
            }*/
            /*for($ix = 0; $ix < count($unic); $ix++) {
                foreach ($unic as $item) {
                    if($item != )
                }
            }*/
//            print_r ($unic);
            return $goods;
        }

        if ($_GET['sort_id']) {
            $id = strip_tags($_GET['sort_id']);
            $goods = get_goods($db, $id);
            foreach ($goods as $item) {
                ?>
                <article class="post type-post status-publish format-standard has-post-thumbnail hentry">
                    <div class="entry-thumb">
                        <?php /*foreach ($goods as $item_in)
            {
            if ($item['id'] == $item_in['post_id']) { */ ?><!--
                <?/*= $item['id'] */ ?>
                <?/*= $item['post_id'] */ ?>
                --><?php
                        /*                break;
                                    }
                                }
                                                */ ?>

                        <?php
                        $item_id = $item['id'];
                        $sql_disc = "SELECT pmet.meta_value FROM wp_posts p, wp_postmeta pmet, wp_terms t
                            WHERE p.post_status != 'trash'
                            AND pmet.meta_key = 'DC'
                            AND pmet.post_id =  $item_id";
                        $result_sql_disc = mysqli_query($db, $sql_disc) or die(mysqli_error($db));
                        $result_sql_disc_final = mysqli_fetch_assoc($result_sql_disc);
                        /*foreach ($goods as $item_in_disc)
                        {
                            if ($item['id'] == $item_in_disc['post_id']) { */ ?><!--
                        <?/*= $item['discount'] */ ?>
                        --><?php
                        /*                        break;
                                            }
                                        }*/
                        ?>
                        <?php if ($result_sql_disc_final['meta_value']) { ?>
                            <img class="discount-image" src="/wp-content/themes/sydney/img/discount.png">
                            <span style="right: 3%; top: 5%;"
                                  class="discount-amount"><?= $result_sql_disc_final['meta_value'] ?></span>
                            <?php
                        }
                        ?>

                        <span class="custom-size"><img src="<?= $item['guid'] ?>" </span>
                    </div>

                    <header class="entry-header">
                        <h2 class="title-post"><span id="country-title" class="country-title-class"
                                                     style="color: #0088e7;"
                                                     rel="bookmark"><?= $item['post_title'] ?> </span></h2>
                    </header><!-- .entry-header -->

                    <div class="entry-post">
                        <p><?= $item['post_content'] ?></p>
                    </div><!-- .entry-post -->

                    <footer class="entry-footer">
                        <?php //sydney_entry_footer(); ?>
                        <div class="tprice">
                            <div>от <strong><b><?= $item['name'] ?>
                                        <style>b a {
                                                color: #0088e7;
                                            }</style>
                                    </b></strong> $
                            </div>
                            <img style="display: none;" id="im"
                                 src="/wp-content/themes/sydney/img/icons/privilege2.png">
                            <a id="order-special-button" onclick="addhotel(this);" rel="fancybox" href="#"
                               class="popmake-form_for_special btnprice modal-link product-link"
                               data-order="AMC Royal Hotel 5*" country="Египет">Заказать</a>
                        </div>
                    </footer><!-- .entry-footer -->
                </article><!-- #post-## -->
                <?php
            }
            exit;
        } else {
            $goods = get_goods($db);
        }
    } ?>


