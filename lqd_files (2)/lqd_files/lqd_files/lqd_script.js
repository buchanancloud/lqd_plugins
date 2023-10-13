jQuery(document).ready(function($) {
    var minimizedHeight = lqdData.minimizedHeight + "px";
    var originalHeight = lqdData.originalHeight + "px";
    var startMinimized = lqdData.startMinimized === '1';
    var autoSlideDelay = parseInt(lqdData.autoSlideDelay);
    var width = lqdData.width + "px"; // New setting for the width of the vertical bar

    var $bar = $('.lqd-bar');
    var $verticalBar = $('.lqd-vertical-bar'); // Select the vertical bar element
    var $toggle = $('.lqd-toggle');

    if (startMinimized) {
        $bar.css('height', minimizedHeight);
        $verticalBar.css('width', width); // Set the width of the vertical bar
        $toggle.text('+');
    }

    if (autoSlideDelay > 0) {
        setTimeout(function() {
            if (startMinimized && $bar.height() <= parseInt(minimizedHeight)) {
                $bar.animate({height: originalHeight});
                $verticalBar.css('width', width); // Set the width of the vertical bar
                $toggle.text('-');
            } else if (!startMinimized && $bar.height() >= parseInt(originalHeight)) {
                $bar.animate({height: minimizedHeight});
                $verticalBar.css('width', width); // Set the width of the vertical bar
                $toggle.text('+');
            }
        }, autoSlideDelay);
    }

    $toggle.click(function() {
        if ($bar.height() > parseInt(minimizedHeight)) {
            $bar.animate({height: minimizedHeight});
            $verticalBar.css('width', width); // Set the width of the vertical bar
            $(this).text('+');
        } else {
            $bar.animate({height: originalHeight});
            $verticalBar.css('width', width); // Set the width of the vertical bar
            $(this).text('-');
        }
    });
});