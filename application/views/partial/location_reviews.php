<?php if (count($reviews)) { ?>
    <ul class="list-group">
        <?php foreach ($reviews as $review) { ?>
            <li class="list-group-item list-group-item-action p-0">
                <div class="card border-0 bg-0">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $review['name']; ?><span class="badge badge-secondary float-right">
                            <?php echo rating_stars($review['rating']); ?>
                        </span></h3>
                        <p class="card-text"><?php echo $review['review']; ?></p>
                        <div class="card-footer border-0 bg-0 p-0 text-muted">
                            <?php echo carbon()->parse($review['added'])->diffForHumans(); ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>There are currently no reviews for <?php echo $location['name']; ?>.</p>
<?php } ?>