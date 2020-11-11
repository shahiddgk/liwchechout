<h1>History</h1>
<div class="wrap" id="clubs-documents-section">
    <?php if( $attributes["all_clubs"] ) : ?>
    <div class="select-clubs">
        <select class="choice-club" name="choice-club">
            <option value="all">All Clubs</option>
            
                <?php foreach ($attributes["all_clubs"] as $kclub => $vclub) : ?>
                    <?php $selcted = selected($vclub->club_id, $attributes["selected_club"], false); ?>
                    <option value="<?php echo $vclub->club_id; ?>" <?php echo $selcted; ?>><?php echo $vclub->club_name; ?></option>
                <?php endforeach; ?>
            
        </select>
    </div>
    <?php endif; ?>
    <?php 
        global $wp;
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
        print_r($wp->query_string);
    ?>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions"></div>
        <div class="tablenav-pages one-page"><span class="displaying-num"><span class="item-num"><?php echo $attributes["dbd_count"]; ?></span> items</span>
<!--            <span class="pagination-links"><span class="tablenav-pages-navspan" aria-hidden="true">«</span>-->
<!--                <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>-->
<!--                <span class="paging-input">-->
<!--                    <label for="current-page-selector" class="screen-reader-text">Current Page</label>-->
<!--                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging" />-->
<!--                    <span class="tablenav-paging-text"> of <span class="total-pages">1</span></span>-->
<!--                </span>-->
<!--                <span class="tablenav-pages-navspan" aria-hidden="true">›</span>-->
<!--                <span class="tablenav-pages-navspan" aria-hidden="true">»</span>-->
<!--            </span>-->
        </div>
        <br class="clear">
    </div>
    <table class="wp-list-table widefat striped term history-clubs">
        <thead>
            <tr>
                <?php
                    $club_is_selected = ( $attributes["selected_club"] && $attributes["selected_club"] !== 'all' ) ? '&club_id='.$attributes["selected_club"] : null;
                ?>
                <th class="manage-column th-history-column">
                    <span>
                        <a href="<?php echo admin_url('admin.php?page=clubs-history'.$club_is_selected.'&order_by=id&order='.$attributes["order"]); ?>">Document ID</a>
                    </span>
                </th>
                <?php if( $attributes["all_clubs"] ) : ?>
                <th class="manage-column th-history-column">
                    <span>
                        <a href="<?php echo admin_url('admin.php?page=clubs-history'.$club_is_selected.'&order_by=club_name&order='.$attributes["order"]); ?>">Club Name</a>
                    </span>
                </th>
                <?php endif; ?>
                <th class="manage-column th-history-column">
                    <span>
                        <a href="<?php echo admin_url('admin.php?page=clubs-history'.$club_is_selected.'&order_by=open_date&order='.$attributes["order"]); ?>">Date of opening</a>
                    </span>
                </th>
                <th class="manage-column th-history-column">
                    <span>
                        <a href="<?php echo admin_url('admin.php?page=clubs-history'.$club_is_selected.'&order_by=close_date&order='.$attributes["order"]); ?>">Date of closing</a>
                    </span>
                </th>
                <th class="manage-column th-history-column">
                    <span>
                        <a href="<?php echo admin_url('admin.php?page=clubs-history'.$club_is_selected.'&order_by=create_date&order='.$attributes["order"]); ?>">Date of creation</a>
                    </span>
                </th>
                <th class="manage-column th-history-column th-history-btn">View details</th>
                <?php if( $attributes["all_clubs"] ) : ?>
                <th class="manage-column th-history-column th-history-btn">Delete period</th>
                <?php endif; ?>
            </tr>
        </thead>
        <?php foreach ( $attributes["db_data"] as $key => $val ) : ?>
        <tr data-row="<?php echo $val->id; ?>" class="tr-history-row">
            <td><?php echo $val->id; ?></td>
            <?php if( $attributes["all_clubs"] ) : ?>
                <td><a href="<?php echo admin_url('term.php?taxonomy=clubs&tag_ID='.$val->club_id.'&post_type=product'); ?>" target="_blank"><?php echo $val->club_name; ?></a></td>
            <?php endif; ?>
            <td class="bkg-open-date"><?php echo $val->open_date; ?></td>
            <td class="bkg-close-date"><?php echo $val->close_date; ?></td>
            <td class="bkg-create-date"><?php echo $val->create_date; ?>
                <?php if( $val->update_date ) : ?>
                <br/><small style="color: green;">Update on: <?php echo $val->update_date; ?></small>
                <?php endif; ?>
            </td>
            <td class="td-history-btn">
                <?php
                    $open = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $val->open_date ) );
                    $close = \LW\Settings::getDateFormat('this_mysql_date',array('date' => $val->close_date ) );
                ?>
                <a href="<?php echo admin_url('admin.php?page=clubs-history&club_id='.$val->club_id.'&open_date='.$open.'&close_date='.$close.'&section=pdf'); ?>" data-club-id="<?php echo $val->club_id; ?>" target="_blank" class="button button-primary">Packing Slip</a>
                <a href="<?php echo admin_url('admin.php?page=clubs-history&club_id='.$val->club_id.'&open_date='.$open.'&close_date='.$close.'&section=invoice'); ?>" target="_blank" class="button button-primary">Invoices</a>
            </td>
            <?php if( $attributes["all_clubs"] ) : ?>
            <td class="td-history-btn">
                <input type="checkbox" name="del-period-row" value="<?php echo $val->id; ?>" />
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php if( $attributes["all_clubs"] ) : ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="del-message"></td>
            <td class="td-history-btn">
                <div id="btn-delete-history" class="btn-delete">Delete</div>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>