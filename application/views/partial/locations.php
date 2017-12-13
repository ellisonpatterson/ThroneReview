<?php if (count($locations)) { ?>
    <ul class="list-group">
        <?php foreach ($locations as $location) { ?>
            <li class="list-group-item list-group-item-action p-0">
                <a href="<?php echo base_url('locations/overlay?location_id=' . $location['location_id']); ?>" data-toggle="modal" class="no-hover">
                    <div class="card border-0 bg-0">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $location['name']; ?>
                                <?php if ($location['avg_rating']) { ?>
                                    <span class="badge badge-secondary float-right">
                                        <?php echo rating_stars($location['avg_rating']); ?>
                                    </span>
                                <?php } ?>
                            </h3>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $location['address']; ?></h6>
                        </div>
                    </div>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>There are currently no locations available.</p>
<?php } ?>