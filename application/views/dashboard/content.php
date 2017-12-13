<div class="row d-flex flex-row h-100">
    <div class="col-sm-12 col-lg-8 px-0 h-100">
        <?php echo $mapsHtml; ?>
    </div>

    <div class="col-sm-12 col-lg-4 py-3" id="dashboardTab" role="tablist">
        <ul class="nav nav-pills flex-column flex-sm-row">
            <li class="col-sm-4 px-0 nav-item">
                <a id="recent-tab" class="flex-sm-fill text-sm-center nav-link active" data-toggle="tab" role="tab" aria-controls="recent" aria-selected="true" href="#recent">Recent</a>
            </li>

            <li class="col-sm-4 px-0 nav-item">
                <a id="popular-tab" class="flex-sm-fill text-sm-center nav-link tab-ajax" data-url="<?php echo base_url('locations/popular'); ?>" data-toggle="tab" role="tab" aria-controls="popular" aria-selected="false" href="#popular">Popular</a>
            </li>

            <li class="col-sm-4 px-0 nav-item">
                <a id="nearby-tab" class="flex-sm-fill text-sm-center nav-link tab-ajax" data-url="<?php echo base_url('locations/nearby'); ?>" data-params="findNearbyLocation" data-toggle="tab" role="tab" aria-controls="nearby" aria-selected="false" href="#nearby">Nearby</a>
            </li>
        </ul>

        <div class="tab-content py-3">
            <div class="tab-pane fade show active" id="recent" role="tabpanel" aria-labelledby="recent-tab">
                <?php echo $this->load->view('partial/reviews', array(
                    'reviews' => $recentReviews
                ), true); ?>
            </div>

            <div class="tab-pane fade" id="popular" role="tabpanel" aria-labelledby="popular-tab">
            
            </div>

            <div class="tab-pane fade" id="nearby" role="tabpanel" aria-labelledby="nearby-tab">
            
            </div>
        </div>
    </div>
</div>