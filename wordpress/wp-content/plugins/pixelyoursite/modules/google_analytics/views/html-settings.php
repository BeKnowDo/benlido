<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<h2 class="section-title">Google Analytics Settings</h2>

<!-- General -->
<div class="card card-static">
	<div class="card-header">
		General
	</div>
	<div class="card-body">
        <div class="row mb-3">
            <div class="col">
                <?php GA()->render_switcher_input( 'enabled' ); ?>
                <h4 class="switcher-label">Enable Google Analytics</h4>
            </div>
        </div>
		<div class="row">
			<div class="col">
				<?php GA()->render_switcher_input( 'enhance_link_attribution' ); ?>
				<h4 class="switcher-label">Enable Enhance Link Attribution</h4>
			</div>
        </div>
        <div class="row">
            <div class="col">
				<?php GA()->render_switcher_input( 'anonimize_ip' ); ?>
                <h4 class="switcher-label">Anonimize IP</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-11 col-offset-left">
                <div class="indicator indicator-off">OFF</div>
                <h4 class="indicator-label">Tracking Custom Dimensions</h4>
            </div>
            <div class="col-1">
		        <?php renderExternalHelpIcon( 'https://www.pixelyoursite.com/documentation/google-analytics-custom-dimensions?utm_source=pixelyoursite-free-plugin&utm_medium=plugin&utm_campaign=free-plugin-analytics-settings' ); ?>
            </div>
        </div>
	</div>
</div>

<div class="panel">
    <div class="row">
        <div class="col text-center">
            <p class="mb-0">Track more actions with the PRO version.
                <br><a href="https://www.pixelyoursite.com/google-analytics?utm_source=pixelyoursite-free-plugin&utm_medium=plugin&utm_campaign=free-plugin-analytics-settings"
                        target="_blank">Find more about the Google Analytics pro implementation</a></p>
        </div>
    </div>
</div>

<hr>
<div class="row justify-content-center">
	<div class="col-4">
		<button class="btn btn-block btn-save">Save Settings</button>
	</div>
</div>