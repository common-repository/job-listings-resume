<?php
/**
* Display message when cannot view resume list.
*
* This template can be overridden by copying it to yourtheme/job-listings/resume/cannot-view.php.
*
* HOWEVER, on occasion NooTheme will need to update template files and you
* (the theme developer) will need to copy the new files to your theme to
* maintain compatibility. We try to do this as little as possible, but it does
* happen. When this occurs the version of the template file will be bumped and
* the readme will list any important changes.
*
* @author      NooTheme
* @version     1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$html = jlt_resume_not_view_html();

?>
<div class="jlt-cannot-view jlt-cannot-view-resume">
    <p>
        <?php echo $html['title']; ?>
    </p>
    <?php echo $html['link']; ?>
</div>
