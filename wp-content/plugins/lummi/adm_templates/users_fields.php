<?php
    $clubs = get_terms('clubs',array(
        'hide_empty' => false,
        'fields' => 'all'
    ));

?>
<h3>Club Membership</h3>
<table class="form-table">
    <tr>
        <th><label for="club_id">Clubs:</label></th>
        <td>
            <select id="user_club" name="user_club">
                <?php
                foreach ($clubs as $club):
                        $selected = (get_the_author_meta( 'user_club', $attributes['user_id'] ) == $club->term_id) ? 'selected' : null;
                    if(!get_the_author_meta( 'user_club', $attributes['user_id'] ) && get_term_meta($club->term_id, 'default_club', true) == 'default_club'){
                        $selected = 'selected';
                    }
                    ?>
                    <option value="<?php echo $club->term_id;?>" <?php echo $selected;?>><?php echo $club->name; ?></option>
                <?php endforeach; ?>
            </select><br/>
            <span class="description">Add to club</span>
        </td>
    </tr>
</table>