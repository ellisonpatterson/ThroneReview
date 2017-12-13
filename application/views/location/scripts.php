<script src="<?php echo base_url(); ?>assets/js/starrr.js"></script>
<script type="text/javascript">
    $(function() {
        return $('#rating_visible').starrr({
            rating: <?php echo (!empty($userReview) ? $userReview['rating'] : 0); ?>
        });
    });

    $(document).ready(function() {
        $('#rating_visible').on('starrr:change', function(e, value) {
            $('#rating_hidden').val(value);
        });
    });
</script>