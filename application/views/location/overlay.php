<?php
    if (is_modal()) {
        global $modalFooter;
        $modalFooter = '
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <a href="' . base_url('locations/' . $location['location_id']) . '" class="btn btn-primary">View Details</a>
        ';
    }
?>

<ul class="list-group">
    <li class="list-group-item border-0 p-0">
        <div class="card border-0 bg-0">
            <div class="card-body">
                <h3 class="card-title">
                    <?php echo $location['name']; ?>
                    <?php if ($averageRating) { ?>
                        <span class="badge badge-secondary float-right">
                            <?php echo rating_stars($averageRating); ?>
                        </span>
                    <?php } ?>
                </h3>

                <h6 class="card-subtitle mb-2 text-muted"><?php echo $location['address']; ?></h6>
                <hr class="my-4">
                <?php if (!empty($reviews)) { ?>
                    <ul class="list-group border-0">
                        <?php foreach ($reviews as $review) { ?>
                            <li class="list-group-item border-0 p-0">
                                <div class="card border-0 rounded-0 bg-0">
                                    <div class="card-body p-0">
                                        <h4 class="card-title"><?php echo $review['name']; ?><span class="badge badge-secondary float-right">
                                            <?php echo rating_stars($review['rating']); ?>
                                        </span></h4>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="card-text">There are current no reviews for <?php echo $location['name']; ?>.</p>
                <?php } ?>
            </div>
        </div>
    </li>
</ul>