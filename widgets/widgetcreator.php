<?php

class WidgetCreator {
	function register() {
        $callback = function($params) {
            // your code that executes before $user is deleted
        };
        $userManager->listen('OC_DASHBOARD', 'preDelete', $callback);
	}
}