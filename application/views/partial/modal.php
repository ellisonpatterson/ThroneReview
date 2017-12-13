<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <?php if (isset($showHeader) && $showHeader) { ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel"><?php echo $title ?? 'Website'; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <div class="modal-body">
            <div class="container">
                <?php echo $content; ?>
            </div>
        </div>

        <?php if (isset($modalFooter)) { ?>
            <div class="modal-footer">
                <?php echo $modalFooter; ?>
            </div>
        <?php } ?>
    </div>
</div>