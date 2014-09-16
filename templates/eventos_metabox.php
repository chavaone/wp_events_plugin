<?php
      // - grab data -
  global $post;
  $custom = get_post_custom($post->ID);
  $meta_date = null;
  if (isset($custom["event_date"][0])) {$meta_date = $custom["event_date"][0];}
  $meta_time = $meta_date;

  // - grab wp time format -

  $date_format = get_option('date_format'); // Not required in my code
  $time_format = get_option('time_format');

  // - populate today if empty, 00:00 for time -

  if ($meta_date == null) { $meta_date = time(); $meta_time = 0;}

  // - convert to pretty formats -

  $meta_date = date("D, M d, Y", $meta_date);
  $meta_time = date($time_format, $meta_time);

  ?>
  <div class="tf-meta">
      <?php wp_nonce_field( basename( __FILE__ ), 'event_post_nonce' ); ?>
      <ul>
          <li>
            <label>Data</label>
            <input type="text" name="eventos_date" id="event_date_input" value="<?php echo $meta_date; ?>" />
          </li>
          <li>
            <label>Hora</label>
            <input name="eventos_time" value="<?php echo $meta_time; ?>" />
            <em>Emprega formato 24h (7pm = 19:00)</em>
            </li>
      </ul>
  </div>