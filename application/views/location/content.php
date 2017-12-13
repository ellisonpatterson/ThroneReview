<div class="row d-flex flex-row h-100">
    <div class="col-sm-12 col-lg-8 px-0">
        <div class="jumbotron jumbotron-fluid h-100 py-5 mb-0">
            <div class="container">
                <h1 class="display-4"><?php echo $location['name']; ?></h1>
                <?php if (count($reviews)) { ?>
                    <h3>
                        <span class="badge badge-secondary">
                            <?php echo rating_stars($averageRating); ?>
                        </span>
                    </h3>
                <?php } ?>
                <p class="lead"><?php echo $location['address']; ?></p>
                <hr class="my-4">
                <?php if ($this->user->isLoggedIn()) { ?>
                    <form  method="post" action="<?php echo base_url('reviews/' . $location['location_id'] . '/' . (empty($userReview) ? 'create' : 'update')); ?>">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <div class="form-group">
                            <label for="rating_visible">Rating</label>
                            <div id="rating_visible" class="starrr"></div>
                            <input id="rating_hidden" name="rating" type="hidden" value="<?php echo (!empty($userReview) ? $userReview['rating'] : ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="review">Review</label>
                            <textarea class="form-control" id="review" name="review" rows="3" required><?php echo (!empty($userReview) ? $userReview['review'] : ''); ?></textarea>
                        </div>

                        <?php if (empty($userReview)) { ?>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        <?php } else { ?>
                            <button type="submit" class="btn btn-primary">Update Review</button>
                            <a href="<?php echo base_url('reviews/' . $userReview['review_id'] . '/delete'); ?>" class="btn btn-danger">Delete Review</a>
                        <?php } ?>
                    </form>
                <?php } else { ?>
                    <p class="lead">You must be logged in to create a review for <?php echo $location['name']; ?>.</p>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-4 py-3" id="locationTab" role="tablist">
        <ul class="nav nav-pills flex-column flex-sm-row">
            <li class="col-sm-6 px-0 nav-item">
                <a id="reviews-tab" class="flex-sm-fill text-sm-center nav-link active" data-toggle="tab" role="tab" aria-controls="reviews" aria-selected="true" href="#reviews">Reviews</a>
            </li>

            <li class="col-sm-6 px-0 nav-item">
                <a id="nearby-tab" class="flex-sm-fill text-sm-center nav-link tab-ajax" data-url="<?php echo base_url('locations/nearby'); ?>" data-params="findNearbyLocation" data-toggle="tab" role="tab" aria-controls="nearby" aria-selected="false" href="#nearby">Nearby Locations</a>
            </li>
        </ul>

        <div class="tab-content py-3">
            <div class="tab-pane fade show active" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <?php echo $this->load->view('partial/location_reviews', array(
                    'reviews' => $reviews,
                    'location' => $location
                ), true); ?>
            </div>

            <div class="tab-pane fade" id="nearby" role="tabpanel" aria-labelledby="nearby-tab">
            
            </div>
        </div>
    </div>
</div>